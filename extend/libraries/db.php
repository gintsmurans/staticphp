<?php

class db
{
  public static $queries = NULL;
  public static $query_count = NULL;

  private static $db_links;
  private static $last_statement;


  // -- INIT
  public static function init($name = 'default')
  {
    // Check if there is such configuration
    if (empty(load::$config['db'][$name]))
    {
      return FALSE;
    }

    // Set params
    $params = load::$config['db'][$name];

    // Don't make a new connection if there is one connected with the name
    if (!empty(self::$db_links[$name]))
    {
      return self::$db_links[$name];
    }

    // Set default connection
    if (empty(self::$db_links['default']))
    {
      self::$db_links['default'] = &self::$db_links[$name];
    }

    // Open new connection to DB
    self::$db_links[$name] = new PDO($params['string'], $params['username'], $params['password'], array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_CASE => PDO::CASE_NATURAL,
      PDO::ATTR_PERSISTENT => $params['persistent']
    ));

    // Set encoding - for mysql
    if (!empty($params['charset']))
    {
      self::$db_links[$name]->exec('SET NAMES '. $params['charset'] .';');
    }
  }



	// -- QUERY
  public static function query($query, $data = NULL, $name = 'default')
  {
    $db_link = &self::init($name);

    if (!empty($query))
    {
      if (empty($db_link))
      {
        throw new Exception('No connection to database');
      }
      else
      {
        // Do request
        self::$last_statement = $db_link->prepare($query);
        self::$last_statement->setFetchMode(PDO::FETCH_OBJ);
        self::$last_statement->execute((array) $data);

        // Count Queries
        if (!empty(load::$config['debug']))
        {
          ++self::$query_count;
          self::$queries[$name][] = self::$last_statement->queryString;
        }

        // Return last statement
        return self::$last_statement;
      }
    }
  }



	// -- Fetch wrapper
  public static function fetch($query, $data = array(), $name = 'default')
  {
    return self::query($query, $data, $name)->fetch();
  }



	// -- FetchAll wrapper
  public static function fetchAll($query, $data = array(), $name = 'default')
  {
    return self::query($query, $data, $name)->fetchAll();
  }



	// -- Exec
  public static function exec($query, $data = NULL, $name = 'default')
  {
    $db_link = &self::init($name);

    if (!empty($query))
    {
      if (empty($db_link))
      {
        throw new Exception('No connection to database');
      }
      else
      {
        // Create NULL return value
        $prepare = NULL;

        // Try execute query
        try
        {
          $db_link->beginTransaction();
          $prepare = self::query($query, (array) $data);
          $db_link->commit();
        }
        catch(PDOException $e)
        {
          $db_link->rollback();
          throw new Exception($e->getMessage());
        }

        return $prepare;
      }
    }
  }



	// -- Make update string from and array. Add "!" at start of the key to avoid escaping. Can be also used for WHERE statements, when delimeter is set to ' AND '.
  public static function make_update(&$data, $delimiter = ', ')
  {
    foreach ((array)$data as $key => $value)
    {
			$c = '=';
			$expl = explode(' ', $key);
			if (count($expl) > 1)
			{
				unset($data[$key]);
				$key = $expl[0];
				$c = $expl[1];
				$data[$key] = $value;
			}
			
      if ($key[0] == '!')
      {
        $set[] = substr($key, 1) ." {$c} {$value}";
        unset($data[$key]);
      }
      else
      {
        $set[] = "{$key} $c :{$key}";
      }
    }

    return (empty($set) ? '' : implode($delimiter, $set));
  }



	// -- Make insert string from and array. Add "!" at start of the key to avoid escaping
  public static function make_insert(&$data)
  {
    foreach ((array)$data as $key => $value)
    {
      if ($key[0] == '!')
      {
        $values[substr($key, 1)] = $value;
        unset($data[$key]);
      }
      else
      {
        $values[$key] = ':'.$key;
      }
    }

    return '('. implode(',', array_keys((array)$values)) .') VALUES ('. implode(',', $values) .')';
  }



	// -- Return link to the database connection for raw actions on it
  public static function &db_link($name = 'default')
  {
    return self::$db_links[$name];
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
    return empty(self::$last_statement) ? NULL : self::$last_statement->queryString;
  }



	// -- Return the last insert id is created into database
  public static function last_insert_id($sql = FALSE, $name = 'default')
  {
		if (empty($sql))
		{
			$db_link = &self::$db_links[$name];
			return $db_link->lastInsertId();
		}
		else
		{
			$res = self::query('SELECT LAST_INSERT_ID() as id');
			return (empty($res->id) ? NULL : $res->id);
		}
  }
}

?>