<?php

class load
{
  public static $config = array();


// ------------------ CONFIG -----------------------

  # Get config variable
  public static function &get($name)
  {
    if (isset(self::$config[$name]))
    {
      return self::$config[$name];
    }
    
    return NULL;
  }



  # Set config variable
  public static function set($name, $value)
  {
    return (self::$config[$name] = $value);
  }



  # Merge config variable
  public static function merge($name, $value, $owerwrite = TRUE)
  {
    if (!isset(self::$config[$name]))
    {
      return (self::$config[$name] = $value);
    }

    switch (true)
    {
      case is_array(self::$config[$name]):
        if (empty($owerwrite))
        {
          return (self::$config[$name] += $value);
        }
        else
        {
          return (self::$config[$name] = array_merge((array)self::$config[$name], (array)$value));
        }
      break;

      case is_object(self::$config[$name]):
        if (empty($owerwrite))
        {
          return (self::$config[$name] = (object)((array)self::$config[$name] + (array)$value));
        }
        else
        {
          return (self::$config[$name] = (object)array_merge((array)self::$config[$name], (array)$value));
        }
      break;

      case is_int(self::$config[$name]):
      case is_float(self::$config[$name]):
        return (self::$config[$name] += $value);
      break;

      case is_string(self::$config[$name]):
      default:
        return (self::$config[$name] .= $value);
      break;
    }
  }




// ------------------ LOADING OF FILES -----------------------

  # Load config files
  public static function config($files)
  {
    foreach ((array) $files as $name)
    {
      $filename = APP_PATH .'config/'. $name .'.php';
      if (is_file($filename))
      {  
        include $filename;
        if (!empty($config))
        {
          self::$config += (array) $config;
        }
      }
    }
  }


  # Load libraries
  public static function library($files)
  {
    foreach ((array) $files as $name)
    {
      $filename = APP_PATH .'libraries/'. $name .'.php';
      if (is_file($filename))
      {
        include $filename;
        self::config($name);
      }
    }
  }


  # Load a views
  function view($files, $data = array())
  {
    // Check for global template variables
    if (!empty(self::$config['view_data']))
    {
      $data = $data + (array) self::$config['view_data'];
    }

    foreach ((array) $files as $file)
    {
      // Make filename
      $filename = APP_PATH . 'views/' . $file . '.php';
      if (is_file($filename))
      {
        include $filename;
      }
    }
  }


  # Load any file within application directory or full path to a file
  function helper($files)
  {
    foreach ((array) $files as $name)
    {
      $filename = APP_PATH .'helpers/'. $name .'.php';
      if (is_file($filename))
      {
        include $filename;
      }
    }
  }

}

?>