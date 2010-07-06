<?php

/*
  Sessions memcached / SQL class. Session classes are not static because of php 5.3 behaviour in destructing classes.
  For table structure look in extend/db_scripts/table_sessions.sql
*/

class session
{
  private $prefix = NULL;     // Session prefix
  private $expire = NULL;     // Expire time
	private $use_sql = FALSE;   // Use sql backend or not
	private $memcached = NULL;  // Resource to memcached


	// -- Constructor
  public function __construct($memcached, $use_sql = FALSE)
  {
    $this->prefix = session_name();
    $this->expire = session_cache_expire() * 60;

    $this->memcached = $memcached;
    $this->use_sql = $use_sql;

    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);

    session_set_save_handler(
      array($this, 'open'),
      array($this, 'close'),
      array($this, 'read'),
      array($this, 'write'),
      array($this, 'destroy'),
      array($this, 'gc')
    );
    session_start();
  }


  public function __destruct()
  {
    session_write_close();
  }
  
  public function open()
  {
    return TRUE;
  }
  
  public function close()
  {
    return TRUE;
  }


	// -- Read session data
  public function read($id)
  {
    $data = $this->memcached->get($this->prefix . $id);
    
    if (!empty($data))
    {
      return $data;
    }
    elseif (!empty($this->use_sql))
    {
      $res = db::fetch("SELECT data FROM sessions WHERE id = ?", $id);
  		if (!empty($res->data))
  		{
  		  $this->memcached->set($this->prefix . $id, $res->data, NULL, $this->expire);
  			return $res->data;
  		}
    }
    
    return array();
  }


	// -- Write session data
  public function write($id, $data)
  {
    $this->memcached->set($this->prefix . $id, $data, NULL, $this->expire);
    
    if (!empty($this->use_sql))
    {
      try
      {
        db::query(
          "INSERT INTO sessions VALUES(?, ?, ?);", 
          array($id, time(), $data)
        );  	
    	}
    	catch(Exception $e)
    	{
        $res = db::fetch('
          UPDATE sessions 
          SET 
            id = ?,
            expires = ?,
            data = ?
          WHERE id = ?
        ', array($id, time(), $data, $id));
    	}
    }

    return TRUE;
  }


  public function destroy($id)
  {
  	$this->memcached->delete($this->prefix . $id);
  	if (!empty($this->use_sql))
    {
      db::exec("DELETE FROM `sessions` WHERE `id` = ?", $id);
    }
    return TRUE;
  }


	// -- Garbage collector, not supported for memcache
  public function gc($max)
  {
    if (!empty($this->use_sql))
    {
      db::query("DELETE FROM sessions WHERE expires <= ?", (time() - $max));
    }

    return TRUE;
  }
}

?>