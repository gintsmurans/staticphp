<?php

class load
{
  public static $config = array();

  private static $started_timers = array();
  private static $finished_timers = array();


  /*
  |--------------------------------------------------------------------------
  | Configuration methods
  |--------------------------------------------------------------------------
  */

  # Get config variable
  public static function &get($name)
  {
    return self::$config[$name];
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




  /*
  |--------------------------------------------------------------------------
  | File Loadings
  |--------------------------------------------------------------------------
  */

  # Load config files
  public static function config($files, $project = NULL)
  {
    $config =& self::$config;
    foreach ((array) $files as $name)
    {
      include (empty($project) ? APP_PATH : BASE_PATH . $project . '/') . 'config/'. $name .'.php';
    }
  }


  # Load models
  public static function model($files, $project = NULL)
  {
    foreach ((array) $files as $name)
    {
      include (empty($project) ? APP_PATH : BASE_PATH . $project . '/') . 'models/'. $name .'.php';
    }
  }


  # Load views
  public static function view($files, &$data = array(), $return = FALSE, $project = NULL)
  {
    // Check for global template variables
    if (!empty(self::$config['view_data']))
    {
      $data = $data + (array) self::$config['view_data'];
    }

    // Return it
    if (!empty($return))
    {
      ob_start();
    }

    // Include view files
    foreach ((array) $files as $file)
    {
      include (empty($project) ? APP_PATH : BASE_PATH . $project . '/') . 'views/' . $file . '.php';
    }

    // Return it
    if (!empty($return))
    {
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
  }


  # Helpers
  public static function helper($files, $project = NULL)
  {
    foreach ((array) $files as $name)
    {
      include (empty($project) ? APP_PATH : BASE_PATH . $project . '/') . 'helpers/'. $name .'.php';
    }
  }



  /*
  |--------------------------------------------------------------------------
  | Aditional methods
  |--------------------------------------------------------------------------
  */

  public static function init_timer()
  {
    self::$started_timers[] = microtime(true);
  }

  public static function stop_timer($name)
  {
    self::$finished_timers[$name] = round(microtime(true) - array_shift(self::$started_timers), 5);
  }

  public static function mark_time($name)
  {
    global $microtime;
    self::$finished_timers['*' . $name] = round(microtime(true) - $microtime, 5);
  }

  public static function execution_time()
  {
    global $microtime;
    $output = 'Total execution time: ' . round(microtime(true) - $microtime, 5) . " seconds;\n";
    $output .= 'Memory used: ' . round(memory_get_usage() / 1024 / 1024, 4) . " MB;\n";

    if (!empty(self::$finished_timers))
    {
      foreach (self::$finished_timers as $key => $value)
      {
        $output .= "\n{$key}: {$value} seconds;";
      }
    }

    return $output;
  }
}


// Autoload models
function __autoload($classname)
{
  $classname = ltrim(substr($classname, strrpos($classname, '\\')), '\\');
  \load::model($classname);
}

?>