<?php

/*
  "StaticPHP Framework" - Simple PHP Framework

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


class home
{
  // Variables to pass to a view, I like to keep them in global array
  private static $vars = array();


  // This is called every time controller loads    
  public static function _construct()
  {

  }


  // Default method
  public static function index()
  {
    // Enable timer output
    g('config')->timer = true;

    // Load view
    // Pass array (key => value) as second parameter, to get variables available in your view
    load(array('views/header', 'views/home', 'views/footer'), self::$vars);
  }    
}

?>