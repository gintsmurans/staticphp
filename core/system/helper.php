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


function load_hook($hook)
{
  $tmp = router::url_to_file($hook);

  $file = APP_PATH . $tmp['file'] .'.php';
  if (!in_array($file, get_included_files()))
  {
    include $file;
  }
  call_user_func(array($tmp['class'], $tmp['method']));
}


function load_config($files)
{
  foreach ((array) $files as $name)
  {
    include PUBLIC_PATH .'config/'. $name .'.php';
    if (!empty($config))
  	{
  	 g()->{$name} = (object) $config;
    }
  }
}


function load_lang($files)
{
  foreach ((array) $files as $file)
  {
    include APP_PATH . g('config')->lang_path . '/'. g('config')->language .'/'. $file . '_lang.php';
  }
}



function load($files, $vars = array(), $prefix = null)
{
	// Check for global template variables
	if (!empty(g()->vars))
	{
    $vars = array_merge($vars, (array) g()->vars);
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

  foreach ((array) $files as $file)
  {
    // Make filename
  	$file = rtrim(make_path_string($file), DS).'.php';
  
  	// Check for file existance
  	switch(true)
  	{
  		case is_file(APP_PATH.$file):
        $file = APP_PATH.$file;
  		break;

  		case is_file($file):
  			// do nothing
  		break;
  
  		default:
  			throw new Exception('Can\'t load file: '.$file);
  		break;
  	}

    include $file;
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