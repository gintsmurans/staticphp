<?php

// - Get base uri
function base_url($url = '')
{
  return router::$base_uri . router::trim_slashes($url);
}


// -- Get site uri
function site_url($url = '', $prefix = NULL, $current_prefix = TRUE)
{
  $url002  = !empty($prefix) ? router::trim_slashes($prefix, TRUE) . '/' : '';
  $url002 .= !empty($current_prefix) && !empty(router::$prefixes_uri) ? router::$prefixes_uri . '/' : '';
  return router::$base_uri . $url002 . router::trim_slashes($url);
}



// -- Convert / and \ to systems directory separator
function make_path_string($string)
{
  return str_replace(array('/', '\\'), DS, $string);
}



// -- Load config file from public/config
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


// -- Load any file wihin applications or full path toa file
function load($files, $vars = array(), $prefix = NULL)
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
    switch(TRUE)
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




// -- Function for storing global parameters
function &g($var = NULL)
{
  // Our static object
  static $vars;

  // Init vars object
  if ($vars === NULL)
  {
    $vars = (object)NULL;
  }

  // Set $var
  if (!empty($var) && !isset($vars->{$var}))
  {
    $vars->{$var} = (object)NULL;
  }

  // Return
	if (empty($var))
	{
		return $vars;
	}
	else
	{
		return $vars->{$var};
	}
}



// -- Autoload classes, currently only support for db is available (because of not-to-have-a-messy-code)
function __autoload($class_name)
{
	global $config;
  if ($class_name === 'db' && !empty($config->db[$config->db['autoload']]))
  {
    include_once SYS_PATH . 'db.php';
    db::init($config->db['autoload'], $config->db[$config->db['autoload']], $config->debug);
  }
}

?>