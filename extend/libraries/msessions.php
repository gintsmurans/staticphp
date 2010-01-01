<?php
/*
  "StaticPHP Framework" - Simple PHP Framework
  
  Memcached based sessions handler.
  Requires php "memcached" extension. 

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