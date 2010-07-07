<?php

class router
{
  public static $prefixes = array();
  public static $prefixes_uri = NULL;

  public static $segments = array();
  public static $segments_uri = NULL;
  public static $segments_full = array();
  public static $segments_full_uri = NULL;

  public static $domain_uri = NULL;
  public static $base_uri = NULL;

  public static $file = NULL;
  public static $class = NULL;
  public static $method = NULL;


  public static function init()
  {
    self::split_segments();
    self::load_controller();
  }


  public static function redirect($uri = '', $site_uri = TRUE, $e301 = FALSE, $type = 'http')
  {
    switch ($type)
    {
      case 'js':
        echo '<script type="text/javascript"> window.location.href = \''.($site_uri === FALSE ? $uri : site_url($uri)).'\'; </script>';
      break;
      
      default:
        if ($e301 === TRUE)
        {
            header("HTTP/1.1 301 Moved Permanently");
        }
        
        header("Location: ".(empty($site_uri) ? $uri : site_url($uri)));
        header("Connection: close");
      break;
    }
    exit;
  }


  public static function have_prefix($p)
  {
    return (isset(self::$prefixes[$p]));
  }


  public static function trim_slashes($s, $booth = FALSE)
  {
    $s = str_replace('\\', '/', $s);
    return (empty($booth) ? ltrim($s, '/') : trim($s, '/'));
  }


  public static function segment($index)
  {
    return (empty(self::$segments[$index]) ? NULL : self::$segments[$index]);
  }


  public static function error($error_code, $error_string)
  {
    header('HTTP/1.0 '. $error_code .' '. $error_string);
    load('views/E'. $error_code);
    exit;
  }


  public static function uri_to_file($uri)
  {
    // Explode $uri
    $tmp = explode('/', $uri);

    // Get class, method and file from $uri
    $data['method'] = array_pop($tmp);
    $data['class'] = end($tmp);
    $data['file'] = implode('/', $tmp);
    return $data;
  }


  public static function split_segments($force = FALSE)
  {
    global $config;
    
    if (empty($force) && !empty(self::$domain_uri))
    {
      return;
    }

    // Get some config variables
    $uri = urldecode($config->request_uri);
    $script_path = self::trim_slashes(dirname($config->script_name), TRUE);

    // Set some variables
    self::$domain_uri = 'http'.(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/';
    self::$base_uri = ($config->base_uri === 'auto' ? self::$domain_uri . (!empty($script_path) ? $script_path.'/' : '') : $config->base_uri);

    // Replace script_path in uri
    $uri = self::trim_slashes(empty($script_path) ? $uri : str_replace('/' . $script_path, '', $uri), TRUE);

    // Check config routing array
    foreach($config->routing as $key => &$item)
    {
      if (!empty($key) && !empty($item))
      {
        $key = str_replace('#', '\\#', $key);
        $tmp = preg_replace('#'.$key.'#', $item, $uri);
        if ($tmp !== $uri)
        {
          $uri = $tmp;
        }
      }
    }

    // Set segments_full_uri
    self::$segments_full_uri = $uri;

    // Remove query string
    $uri = preg_replace('/^(.*?)\?.*/', '$1', $uri);

    // Explode segments
    self::$segments_full = self::$segments = explode('/', $uri);

    // Get URI prefixes
    foreach($config->uri_prefixes as &$item)
    {
      if (isset(self::$segments[0]) && self::$segments[0] == $item)
      {
        array_shift(self::$segments);
        self::$prefixes[$item] = $item;
      }
    }

		// Set URI prefixes uri
    self::$prefixes_uri = implode('/', self::$prefixes);

    // Set URI
    self::$segments_uri = implode('/', self::$segments);

    // Define BASE_URL
		define('BASE_URL', self::$base_uri);
  }



  // ------------ PRIVATE METHODS ----------------------

  private static function load_controller()
  {
		global $config;

    // Get controller, class, method from URI
    $tmp = router::uri_to_file(g('config')->routing['']);

    // Set default class and method
    self::$class = $tmp['class'];
    self::$method = $tmp['method'];

    // If empty segments set file as class name
    if (empty(self::$segments[0]))
    {
      self::$file = $tmp['file'];
    }
    else
    {
      self::$file = self::$segments[0];
      $mi = 1;

      // Check for subdirectory
      if (is_dir(APP_PATH .'controllers'. DS . self::$file))
      {
        // Add set class name as segment[1]
        if (!empty(self::$segments[1]))
        {
          self::$class = self::$segments[1];
        }

        // Add class name to self::$file
        self::$file .= '/'.self::$class;

        // Increase method index
        ++$mi;
      }

      // Add default class name to self::$file
      else
      {
        self::$class = self::$file;
      }

      self::$method = (!empty(self::$segments[$mi]) ? self::$segments[$mi] : self::$method);
    }

    // Load pre controller hook
    if (!empty($config->before_controller))
		{
			foreach($config->before_controller as $tmp)
			{
				call_user_func($tmp);
			}
		}

    // Load controllers
    self::_load_controller(APP_PATH .'controllers' . DS . self::$file.'.php', self::$class, self::$method);

    // Unset
    unset($tmp, $mi);
  }



  public static function _load_controller($File, $Class, $Method)
  {
    // Check for $File
    if (is_file($File))
    {
      include $File;

      // Check for $Class
      if (class_exists($Class))
      {
        $methods = array_flip(get_class_methods($Class));
        // Check for $Method
        if (isset($methods[$Method]) || isset($methods['__callStatic']))
        {
          // Call our contructor
          if (isset($methods['_construct']))
          {
            call_user_func(array($Class, '_construct'), $Class, $Method);
          }
          call_user_func(array($Class, $Method));
        }
        else
        {
          $error = 'Class was found, but could not find method: ' . $Method;
        }
      }
      else
      {
        $error = 'File was included, but could not find class: ' . $Class;
      }
    }
    else
    {
      $error = 'Controller file was not found: ' . $File;
    }

    // Show error if there is any
    if (!empty($error))
    {
      if (g('config')->debug === TRUE)
      {
        throw new Exception($error);
      }
      else
      {
        self::error('404', 'Not Found');
      }
    }
    
    unset($methods);
  }
}

?>