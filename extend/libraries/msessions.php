<?php

/*
  !! THIS IS NOT TESTED !!
  Memcached based sessions handler.
  Requires php "memcached" extension.
*/

class msessions
{

  public static function init()
  {
    ini_set('session.save_handler', 'user');
    session_set_save_handler(
      array('msessions', '_open'), 
      array('msessions', '_close'), 
      array('msessions', '_read'), 
      array('msessions', '_write'), 
      array('msessions', '_destroy'), 
      array('msessions', '_gc')
    );
    
    session_start();
  }  
  
  public static function _open()
  {
    return true;
  }

  public static function _close()
  {
    return true;
  }

  public static function _read($id)
  {
    return memcached::get("sessions/{$id}");
  }

  public static function _write($id, $data)
  {
    return memcached::set("sessions/{$id}", $data, ini_get('session.gc_maxlifetime'));
  }

  public static function _destroy($id)
  {
    return memcached::delete("sessions/{$id}");
  }

  public static function _gc($max)
  {
    return true;
  }
}
 
?>