<?php
/*
  "StaticPHP Framework" - Simple PHP Framework

  Controller for handling javascript file with php
  To use add line below to your routing.php file

    'js/.+?\.js' => 'js/index',

  Change "js/.+?\.js" to your javascript directory!

  !! IMPORTANT !!
  Before start writing php scripts in javascript, uncomment lines below in your .htaccess file (change "^js/.*" to your javascript directory)
  
    # RewriteCond %{REQUEST_FILENAME} -f
    # RewriteRule ^js/.* index.php [L]

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


class js
{
  public static function index()
  {
  	$filename = PUBLIC_PATH.implode('/', router::$segments);
  	
    if (!empty(Router::$segments[1]) && is_file($filename))
    {
      // Disable timer output
      g('config')->timer = false;
      
      // Set header and include js file
      header('Content-type: text/javascript; charset=utf-8');
      include_once($filename);
    }
    else
    {
      router::e404();
    }
  }
}

?>