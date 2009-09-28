<?php
/*
  "StaticPHP Framework" - Little PHP Framework
  
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

  Copyright (C) 2009  Gints Murāns <gm@mstuff.org>
*/

class sessions
{
  public static function init()
  {
    ini_set('session.save_handler', 'user');
    session_set_save_handler(
      array('sessions', '_open'), 
      array('sessions', '_close'), 
      array('sessions', '_read'), 
      array('sessions', '_write'), 
      array('sessions', '_destroy'), 
      array('sessions', '_gc')
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
    
    $res = db::query("SELECT `data` FROM `sessions` WHERE `id` = ?", $id)->fetch();
    return (empty($res->data) ? '' : $res->data);
  }

  public static function _write($id, $data)
  {
    db::exec("REPLACE INTO `sessions` VALUES (?, ?, ?)", array($id, $data, time()));
    return true;
  }

  public static function _destroy($id)
  {
    db::exec("DELETE FROM `sessions` WHERE `id` = ?", $id);
    return true;
  }

  public static function _gc($max)
  {
    db::exec("DELETE FROM `sessions` WHERE `expires` <= ?", (time() - $max));
    return true;
  }
}

?>