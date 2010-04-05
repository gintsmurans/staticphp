<?php

/*
  Sessions handling class.
  For table structure look in extend/db_scripts/table_sessions.sql
*/

class sessions
{
  public function __construct()
  {
    ini_set('session.save_handler', 'user');
    session_set_save_handler(
      array('sessions', 'open'), 
      array('sessions', 'close'), 
      array('sessions', 'read'), 
      array('sessions', 'write'), 
      array('sessions', 'destroy'), 
      array('sessions', 'gc')
    );
    session_start();
  }
  
  public function __destruct()
  {
    session_write_close();
  }
  
  public static function open()
  {
    return true;
  }

  public static function close()
  {
    return true;
  }

  public static function read($id)
  {
    
    $res = db::query("SELECT `data` FROM `sessions` WHERE `id` = ?", $id)->fetch();
    return (empty($res->data) ? '' : $res->data);
  }

  public static function write($id, $data)
  {
    db::exec("REPLACE INTO `sessions` VALUES (?, ?, ?)", array($id, $data, time()));
    return true;
  }

  public static function destroy($id)
  {
    db::exec("DELETE FROM `sessions` WHERE `id` = ?", $id);
    return true;
  }

  public static function gc($max)
  {
    db::exec("DELETE FROM `sessions` WHERE `expires` <= ?", (time() - $max));
    return true;
  }
}

?>