<?php

/*
  "StaticPHP Framework" - Simple PHP Framework
  
  ---------------------------------------------------------------------------------
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  ---------------------------------------------------------------------------------
  
  Copyright (C) 2009  Gints Murāns <gm@gm.lv>
*/


class db
{
  public static $queries = null;
  public static $query_count = null;

  private static $db_links;
  private static $last_statement;


  # INIT FUNCTION

  public static function init($scheme = 'default')
  {
    // Don't make a new connection if already connected to the scheme
    if (!empty(self::$db_links[$scheme]))
    {
      return self::$db_links[$scheme];
    }

    // Set default scheme
    if (empty(self::$db_links['default']))
    {
      self::$db_links['default'] = &self::$db_links[$scheme];
    }

    // Get config
    $config = &g('config')->db[$scheme];

    // Open new connection to DB
    self::$db_links[$scheme] = new PDO($config['string'], $config['username'], $config['password'], array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_CASE => PDO::CASE_NATURAL
    ));

    // Set encoding - for mysql
    if (!empty($config['charset']))
    {
      self::$db_links[$scheme]->exec('SET NAMES '. $config['charset'] .';');
    }
  }



  # READ FUNCTIONS

  public static function query($query, $data = null, $scheme = 'default')
  {
    $db_link = &self::init($scheme);

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
        if (g('config')->debug === true)
        {
          ++self::$query_count;
          self::$queries[$scheme][] = self::$last_statement->queryString;
        }
        
        // Return last statement
        return self::$last_statement;
      }
    }
  }


  public static function fetch($query, $data = array(), $scheme = 'default')
  {
    return self::query($query, $data, $scheme)->fetch();
  }


  public static function fetchAll($query, $data = array(), $scheme = 'default')
  {
    return self::query($query, $data, $scheme)->fetchAll();
  }



  # WRITE FUNCTIONS

  public static function exec($query, $data = null, $scheme = 'default')
  {
    $db_link = &self::init($scheme);

    if (!empty($query))
    {
      if (empty($db_link))
      {
        throw new Exception('No connection to database');
      }
      else
      {
        // Create null return value
        $prepare = null;

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


  //
  // Make update from array. Add "!" at start of the key to avoid escaping 
  //
  public static function make_update(&$data, $delimiter = ', ')
  {
    foreach ((array)$data as $key => $value)
    {
      if ($key[0] == '!')
      {
        $set[] = substr($key, 1) ." = {$value}";
        unset($data[$key]);
      }
      else
      {
        $set[] = "{$key} = :{$key}";
      }
    }

    return (empty($set) ? '' : implode($delimiter, $set));
  }


  //
  // Make insert from array. Add "!" at start of the key to avoid escaping 
  //  
  public static function make_insert(&$data)
  {
    foreach ((array)$data as $key => $value)
    {
      if ($key[0] == '!')
      {
        $values[] = $value;
        unset($data[$key]);
      }
      else
      {
        $values[] = ':' . $key;
      }
    }

    return '('. implode(',', array_keys((array)$data)) .') VALUES ('. implode(',', $values) .')';
  }


  public static function insert($table, $data, $scheme = 'default')
  {
    $set = self::make_set($data);
    if (!empty($set))
    {
      self::exec("INSERT INTO {$table} SET ". $set, $data, $scheme);
    }
  }



/*
  public static function update($table, $data, $where = array(), $limit = '')
  {
    $set = self::make_set($data);
    if (!empty($set))
    {
      self::exec("UPDATE {$table} SET ". $set . (empty($where) ? '': " WHERE ". self::make_set($where, ' AND ') ) . (empty($limit) ? '' : 'LIMIT '. $limit), array_merge($data, $where));
    }
  }
*/


  
  # INFO FUNCTIONS

  public static function &db_link($scheme = 'default')
  {
    return self::$db_links[$scheme];
  }
  
  
  public static function &last_statement()
  {
    if (!empty(self::$last_statement))
    {
      return self::$last_statement;
    }
  }


  public static function last_query()
  {
    return empty(self::$last_statement) ? null : self::$last_statement->queryString;
  }
  
  
  public static function last_insert_id($scheme = 'default')
  {
    $db_link = &self::$db_links[$scheme];
    return $db_link->lastInsertId();
  }
}

?>