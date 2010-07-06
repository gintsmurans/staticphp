<?php

/*
  Sessions mySQL database class. Session classes are not static because of php 5.3 behaviour in destructing classes.
  For table structure look in extend/db_scripts/table_sessions.sql
*/

class session
{
  public function __construct()
  {
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
    $res = db::query("SELECT `data` FROM `sessions` WHERE `id` = ?", $id)->fetch();
    return (empty($res->data) ? '' : $res->data);
  }

  public function write($id, $data)
  {
    db::exec("REPLACE INTO `sessions` VALUES (?, ?, ?)", array($id, $data, time()));
    return TRUE;
  }

  public function destroy($id)
  {
    db::exec("DELETE FROM `sessions` WHERE `id` = ?", $id);
    return TRUE;
  }

  public function gc($max)
  {
    db::exec("DELETE FROM `sessions` WHERE `expires` <= ?", (time() - $max));
    return TRUE;
  }
}

?>