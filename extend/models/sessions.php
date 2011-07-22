<?php

/*
  Sessions using MySQL database. Session classes are not static because of php 5.3 behaviour in destructing classes.
  For table structure look in extend/db_scripts/table_sessions.sql
*/

namespace models;

class sessions
{
  public $prefix = NULL;
  public $expire = NULL;

  private $db_link = NULL;

  public function __construct()
  {
    $this->db_link = db::db_link();
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
  	$res = self::query('SELECT "data" FROM "sessions" WHERE "id" = ?', $id)->fetch();
		if (!empty($res->data))
		{
			return $res->data;
		}
    return NULL;
  }


  public function write($id, $data)
  {
    self::destroy($id);
    self::query('INSERT INTO sessions VALUES (?, ?, ?)', array($id, $data, time()));
    return TRUE;
  }


  public function destroy($id)
  {
    self::query('DELETE FROM "sessions" WHERE "id" = ?', $id);
    return TRUE;
  }


  public function gc($max)
  {
    db::query('DELETE FROM "sessions" WHERE "expires" <= ?', (time() - $max));
    return TRUE;
  }


  private function query($query, $data = NULL, $name = 'default')
  {
    if (empty($query))
    {
      return NULL;
    }

    if (empty($this->db_link))
    {
      throw new Exception('No connection to database');
    }

    // Do request
    $prepare = $this->db_link->prepare($query);
    $prepare->execute((array)$data);

    return $prepare;
  }
}

?>