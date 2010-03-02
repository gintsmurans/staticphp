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


function base_url($url = '')
{
  return router::$base_uri . router::trim_slashes($url);
}

function site_url($url = '', $prefix = '', $add_language = 'auto')
{
  $url002  = empty(router::$prefixes_uri) ? '' : router::$prefixes_uri . '/';
  $url002 .= empty($prefix) ? '' : router::trim_slashes($prefix, true) . '/';
	$url002 .= ($add_language === true || ($add_language === 'auto' && g('config')->lang_support === true) ? router::$lang_current['current'] . '/' : '');
  return router::$base_uri . $url002 . router::trim_slashes($url);
}



function make_path_string($string)
{
  return str_replace(array('/', '\\'), DS, $string);
}



function load_hook($hook)
{
  if (!empty(g('config')->hooks[$hook]))
  {
    $tmp = g('config')->hooks[$hook];
    $tmp = router::uri_to_file($tmp);

    $file = APP_PATH . $tmp['file'] .'.php';
    if (!in_array($file, get_included_files()))
    {
      include $file;
    }
    call_user_func(array($tmp['class'], $tmp['method']));
  }
}

function load_config($files)
{
  foreach ((array) $files as $name)
  {
    include PUBLIC_PATH .'config/'. $name .'.php';
    if (!empty($config))
  	{
      g()->config = (object) array_merge((array) g()->config, (array) $config);
    }
  }
}

function load_lang($files)
{
	$dir = APP_PATH . 'languages/' . (empty(router::$lang_current['directory']) ? '' : router::$lang_current['directory'] . '/') . router::$lang_current['current'] .'/';
  foreach ((array) $files as $file)
  {
    include $dir . $file . '_lang.php';
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




function &g($var = null)
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



// AUTOLOAD DB
function __autoload($class_name)
{
  if ($class_name === 'db')
  {
    include_once SYS_PATH.'db.php';
    DB::init();
  }
}

?>