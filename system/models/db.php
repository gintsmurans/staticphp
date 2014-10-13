<?php

namespace models;


class db
{
    private static $db_links;
    private static $last_statement;


    // -- INIT
    public static function init($config = null, $name = 'default')
    {
        // Check if there is such configuration
        if (empty($config))
        {
            if (empty(\load::$config['db']['pdo'][$name]))
            {
                return false;
            }

            $config = \load::$config['db']['pdo'][$name];
        }

        // Don't make a new connection if there is one connected with the name
        if (!empty(self::$db_links[$name]['link']))
        {
            return self::$db_links[$name]['link'];
        }

        // Set config
        self::$db_links[$name]['config'] = $config;

        // Open new connection to DB
        self::$db_links[$name]['link'] = new \PDO($config['string'], $config['username'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
            \PDO::ATTR_DEFAULT_FETCH_MODE => (!empty($config['fetch_mode_objects']) ? \PDO::FETCH_OBJ : \PDO::FETCH_ASSOC),
            \PDO::ATTR_PERSISTENT => $config['persistent']
        ]);

        // Set encoding - mysql only
        if (!empty($config['charset']) && self::$db_links[$name]['link']->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'mysql')
        {
            self::$db_links[$name]['link']->exec('SET NAMES '. $config['charset'] .';');
        }

        return self::$db_links[$name]['link'];
    }



    // -- Query
    public static function query($query, $data = null, $name = 'default')
    {
        $db_link = &self::$db_links[$name]['link'];

        if (empty($query))
        {
            return null;
        }

        if (empty($db_link))
        {
            throw new \Exception('No connection to database');
        }

        // Do request
        if (!empty(self::$db_links[$name]['config']['debug']))
        {
            \load::start_timer();
        }

        self::$last_statement = $db_link->prepare($query);
        self::$last_statement->execute((array)$data);

        if (!empty(self::$db_links[$name]['config']['debug']))
        {
            \load::stop_timer($query . ' [' . implode(', ', (array)$data) . ']');
        }

        // Return last statement
        return self::$last_statement;
    }



    // -- Fetch wrapper
    public static function fetch($query, $data = [], $name = 'default')
    {
        return self::query($query, $data, $name)->fetch();
    }



    // -- FetchAll wrapper
    public static function fetchAll($query, $data = [], $name = 'default')
    {
        return self::query($query, $data, $name)->fetchAll();
    }



    // -- Make update string from and array. Add "!" at start of the key to avoid escaping.
    public static function update($table, $data, $where, $name = 'default')
    {
        // Make SET
        foreach ((array)$data as $key => $value)
        {
            if ($key[0] == '!')
            {
                $set[] = self::$db_links[$name]['config']['wrap_column'] . substr($key, 1) . self::$db_links[$name]['config']['wrap_column'] ." = {$value}";
            }
            else
            {
                $set[] = self::$db_links[$name]['config']['wrap_column'] . $key . self::$db_links[$name]['config']['wrap_column'] .' = ?';
                $params[] = $value;
            }
        }

        // Make WHERE
        foreach ((array)$where as $key => $value)
        {
            $c = '=';
            $expl = explode(' ', $key);
            if (count($expl) > 1)
            {
                $key = $expl[0];
                $c = $expl[1];
            }

            if ($key[0] == '!')
            {
                $cond[] = self::$db_links[$name]['config']['wrap_column'] . substr($key, 1) . self::$db_links[$name]['config']['wrap_column'] . " {$c} {$value}";
            }
            else
            {
                $cond[] = self::$db_links[$name]['config']['wrap_column'] . $key . self::$db_links[$name]['config']['wrap_column'] . " {$c} ?";
                $params[] = $value;
            }
        }

        // Compile SET and WHERE
        $set = implode(', ', $set);
        if (!empty($cond))
        {
            $cond = 'WHERE ' . implode(' AND ', $cond);
        }

        // Run Query
        return self::query("UPDATE {$table} SET {$set} {$cond};", $params, $name);
    }



    // -- Make insert string from and array. Add "!" at start of the key to avoid escaping
    public static function insert($table, $data, $name = 'default')
    {
        foreach ((array)$data as $key => $value)
        {
            if ($key[0] == '!')
            {
                $keys[] = self::$db_links[$name]['config']['wrap_column'] . substr($key, 1) . self::$db_links[$name]['config']['wrap_column'];
                $values[] = $value;
            }
            else
            {
                $keys[] = self::$db_links[$name]['config']['wrap_column'] . $key . self::$db_links[$name]['config']['wrap_column'];
                $values[] = '?';
                $params[] = $value;
            }
        }

        // Compile KEYS and VALUES
        $keys = implode(', ', $keys);
        $values = implode(', ', $values);

        // Run Query
        return self::query("INSERT INTO {$table} ({$keys}) VALUES ({$values})", $params, $name);
    }



    // -- Return link to the database connection for raw actions on it
    public static function &db_link($name = 'default')
    {
        return self::$db_links[$name]['link'];
    }



    // -- Return the last query statement
    public static function &last_statement()
    {
        if (!empty(self::$last_statement))
        {
            return self::$last_statement;
        }
    }



    // -- Return the last query executed
    public static function last_query()
    {
        return empty(self::$last_statement) ? null : self::$last_statement->queryString;
    }



    // -- Return the last insert id is created into database
    public static function last_insert_id($sequence_name = '', $sql = false, $name = 'default')
    {
        if (empty($sql))
        {
            return self::$db_links[$name]['link']->lastInsertId($sequence_name);
        }
        else
        {
            if (empty($sequence_name))
            {
                $res = self::query('SELECT LAST_INSERT_ID() as id');
            }
            else
            {
                $res = self::query('SELECT currval(?) as id', $sequence_name);
            }
            return (empty($res->id) ? null : $res->id);
        }
    }
}

?>