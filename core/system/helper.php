<?php

function base_url($url = '')
{
  return router::$base_uri . router::trim_slashes($url);
}

// prefix == NULL add router::$prefixes
// prefix == false add nothing
// prefix != empty add it
function site_url($url = '', $prefix = NULL, $current_prefix = true)
{
  $url002  = !empty($prefix) ? router::trim_slashes($prefix, true) . '/' : '';
  $url002 .= !empty($current_prefix) && !empty(router::$prefixes_uri) ? router::$prefixes_uri . '/' : '';
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
    db::init();
  }
}

?>