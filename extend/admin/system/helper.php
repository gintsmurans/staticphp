<?php
/*
    "StaticPHP Framework" - Little PHP Framework

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



function site_url($url = '', $prefix = '', $add_language = 'auto')
{
  $url002 = (!empty($prefix) ? Router::trim_slashes($prefix, true).'/' : '') . ($add_language === true || ($add_language === 'auto' && g('config')->lang_redirect === true) ? g('config')->language.'/' : '');
  return g('config')->base_url . $url002 . Router::trim_slashes($url);
}



function base_url($url = '')
{
  return g('config')->base_url . Router::trim_slashes($url);
}



function make_path_string($string)
{
  return str_replace(array('/', '\\'), DS, $string);
}



function load_lang($file)
{
  load('languages/'. g('config')->language .'/'. $file . '_lang');
}



function load($file, $vars = array(), $prefix = null, $return = false)
{
  // Make filename
	$file = rtrim(make_path_string($file), DS).'.php';

	// Check for file existance
	switch(true)
	{
		case is_file($file):
			// do nothing
		break;

		case is_file(APP_PATH.$file):
      $file = APP_PATH.$file;
		break;

		default:
			throw new Exception('Can\'t load file: '.$file);
		break;
	}
	
	// Check for global template variables
	if (!empty(g()->vars))
	{
    $vars = array_merge($vars, (array) g()->vars);
	}
	
	// If return === true
	if ($return === true)
	{
    ob_start();
  }

  // Extract vars	
  if (!empty($vars) && is_array($vars))
  {
  	if (!empty($prefix))
  	{
  		extract($vars, EXTR_PREFIX_ALL, $prefix);
  	}
  	else
  	{
  		extract($vars);
  	}
  }

  include $file;

  // If return === true
	if ($return === true)
	{
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
}




function &g($var = null, $set = null)
{
	// Our static object
	static $vars;

	// Init vars object
	if ($vars === null)
	{
		$vars = (object)null;
	}

  // Set $var
  if (!empty($var) && empty($vars->{$var}))
  {
    $vars->{$var} = (object)null;
  }

	// Set $set
	if ($set !== null)
	{
		$vars->{$var} = (object)$set;
	}

	// Return	
	if (isset($vars->{$var}))
	{
		return $vars->{$var};
	}
	else
	{
		return $vars;
	}
}



// AUTOLOAD
function __autoload($class_name)
{
  if ($class_name === 'db')
  {
    include_once SYS_PATH.'db.php';
    DB::init();
  }
}


?>