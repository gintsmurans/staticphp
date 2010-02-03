<?php
/*
  "StaticPHP Framework" - Simple PHP Framework
  
  Sessions handling class.
  For table structure look in extend/db_scripts/table_sessions.sql

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