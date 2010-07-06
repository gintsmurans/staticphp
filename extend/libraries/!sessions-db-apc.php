<?php

/*
  Sessions APC / mySQL database class. Session classes are not static because of php 5.3 behaviour in destructing classes.
  For table structure look in extend/db_scripts/table_sessions.sql
*/

class sessions
{
  private $prefix = NULL;     // Session prefix
  private $expire = NULL;     // Expire time
	private $use_sql = FALSE;   // Use sql backend or not

  public function __construct($use_sql = FALSE)
  {
    $this->prefix = session_name();
    $this->expire = session_cache_expire() * 60;

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
  
  public function read($id)
  {
    $data = apc_fetch($this->prefix . $id);
    if (!empty($data))
    {
      return $data;
    }
    elseif (!empty($this->use_sql))
    {
    	$res = db::query("SELECT `data` FROM `sessions` WHERE `id` = ?", $id)->fetch();
  		if (!empty($res->data))
  		{
 			  apc_store($this->prefix . $id, $res->data, $this->expire);
  			return $res->data;
  		}
    }
    return array();
  }

  public function write($id, $data)
  {
  	apc_store($this->prefix . $id, $data, $this->expire);
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
  	apc_delete($this->prefix . $id);
  	if (!empty($this->use_sql))
    {
      db::exec("DELETE FROM `sessions` WHERE `id` = ?", $id);
    }
    return TRUE;
  }

  public function gc($max)
  {
    if (!empty($this->use_sql))
    {
      db::exec("DELETE FROM `sessions` WHERE `expires` <= ?", (time() - $max));
    }
    return TRUE;
  }
}

?>