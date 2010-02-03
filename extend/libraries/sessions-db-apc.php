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
  private $prefix = null;
  private $expire = null;

  public function __construct()
  {
    $this->prefix = session_name();
    $this->expire = session_cache_expire() * 60;

    ini_set('session.save_handler', 'user');
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
    return true;
  }
  
  public function close()
  {
    return true;
  }
  
  public function read($id)
  {
    $data = apc_fetch($this->prefix . $id);
  	if ($data === false)
  	{
    	$res = db::query("SELECT `data` FROM `sessions` WHERE `id` = ?", $id)->fetch();
  		if (!empty($res->data))
  		{
  			$data = $res->data;
  			apc_store($this->prefix . $id, $data, $this->expire);
  		}
  	}
    return $data;
  }
  
  public function write($id, $data)
  {
  	apc_store($this->prefix . $id, $data, $this->expire);
    db::exec("REPLACE INTO `sessions` VALUES (?, ". (empty($_SESSION['user']->uid) ? 'NULL' : $_SESSION['user']->uid) .", ?, ?)", array($id, time(), $data));
    return true;
  }
  
  public function destroy($id)
  {
  	apc_delete($this->prefix . $id);
    db::exec("DELETE FROM `sessions` WHERE `id` = ?", $id);
    return true;
  }
  
  public function gc($max)
  {
    db::exec("DELETE FROM `sessions` WHERE `expires` <= ?", (time() - $max));
  
    // Cleanup user other sessions
    if (!empty($_SESSION['user']->uid))
    {
      db::exec("DELETE FROM `sessions` WHERE `uid` = ? AND `id` != ?", array($_SESSION['user']->uid, session_id()));
    }
    return true;
  }
}

?>