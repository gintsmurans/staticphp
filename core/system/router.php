<?php

class router
{
  public static $prefixes = array();
  public static $prefixes_url = NULL;

  public static $segments = array();
  public static $segments_url = NULL;
  public static $segments_full_url = NULL;
	public static $segments_requested = array();

  public static $domain_uri = NULL;
  public static $base_uri = NULL;

  public static $file = NULL;
  public static $class = NULL;
  public static $method = NULL;


  # Init router
  public static function init()
  {
    self::split_segments();
    self::load_controller();
  }


  # Get base uri
  public static function base_uri($url = '')
  {
    return router::$base_uri . router::trim_slashes($url);
  }


  # Get site uri
  public static function site_uri($url = '', $prefix = NULL, $current_prefix = TRUE)
  {
    $url002  = !empty($prefix) ? router::trim_slashes($prefix, TRUE) . '/' : '';
    $url002 .= !empty($current_prefix) && !empty(router::$prefixes_url) ? router::$prefixes_url . '/' : '';
    return router::$base_uri . $url002 . router::trim_slashes($url);
  }


  # Convert / and \ to systems directory separator
  public static function make_path_string($string)
  {
    return str_replace(array('/', '\\'), DS, $string);
  }


  # Redirect
  public static function redirect($url = '', $site_uri = TRUE, $e301 = FALSE, $type = 'http')
  {
    switch ($type)
    {
      case 'js':
        echo '<script type="text/javascript"> window.location.href = \''.($site_uri === FALSE ? $url : router::site_uri($url)).'\'; </script>';
      break;

      default:
        if ($e301 === TRUE)
        {
            header("HTTP/1.1 301 Moved Permanently");
        }

        header("Location: ".(empty($site_uri) ? $url : router::site_uri($url)));
        header("Connection: close");
      break;
    }
    exit;
  }


  # Have prefix
  public static function have_prefix($p)
  {
    return (isset(self::$prefixes[$p]));
  }


  # Trim slahses
  public static function trim_slashes($s, $booth = FALSE)
  {
    $s = str_replace('\\', '/', $s);
    return (empty($booth) ? ltrim($s, '/') : trim($s, '/'));
  }


  # Return segment in url by $index
  public static function segment($index)
  {
    return (empty(self::$segments[$index]) ? NULL : self::$segments[$index]);
  }


  # Show http error
  public static function error($error_code, $error_string)
  {
    header('HTTP/1.0 '. $error_code .' '. $error_string);
    load::view('E'. $error_code);
    exit;
  }


  # Convert url to file path
  public static function url_to_file($url)
  {
    // Explode $url
    $tmp = explode('/', $url);

    // Get class, method and file from $url
    $data['method'] = array_pop($tmp);
    $data['class'] = end($tmp);
    $data['file'] = implode('/', $tmp);
    return $data;
  }


  # Split segments
  public static function split_segments($force = FALSE)
  {
    if (empty($force) && !empty(self::$domain_uri))
    {
      return;
    }

    // Get some config variables
    $uri = urldecode(load::$config['request_uri']);
    $script_path = self::trim_slashes(dirname(load::$config['script_name']), TRUE);

    // Set some variables
    self::$domain_uri = 'http'.(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/';
    self::$base_uri = (load::$config['base_uri'] === 'auto' ? self::$domain_uri . (!empty($script_path) ? $script_path.'/' : '') : load::$config['base_uri']);

    // Replace script_path in uri and remove query string
    $uri = self::trim_slashes(empty($script_path) ? $uri : str_replace('/' . $script_path, '', $uri), TRUE);
    $uri = preg_replace('/\?.*/', '', $uri);

    // Check config routing array
    foreach(load::$config['routing'] as $key => &$item)
    {
      if (!empty($key) && !empty($item))
      {
        $key = str_replace('#', '\\#', $key);
        $tmp = preg_replace('#'.$key.'#', $item, $uri);
        if ($tmp !== $uri)
        {
					self::$segments_requested = explode('/', $uri);
          $uri = $tmp;
        }
      }
    }

    // Set segments_full_url
    self::$segments_full_url = $uri . (empty(load::$config['query_string']) ? '' : '?'. load::$config['query_string']);

    // Explode segments
    self::$segments = explode('/', $uri);

    // Get URL prefixes
    foreach(load::$config['url_prefixes'] as &$item)
    {
      if (isset(self::$segments[0]) && self::$segments[0] == $item)
      {
        array_shift(self::$segments);
        self::$prefixes[$item] = $item;
      }

      if (isset(self::$segments_requested[0]) && self::$segments_requested[0] == $item)
      {
        array_shift(self::$segments_requested);
      }
    }

		// Set URL prefixes url
    self::$prefixes_url = implode('/', self::$prefixes);

    // Set URL
    self::$segments_url = implode('/', self::$segments);

    // Define base_uri
		define('BASE_URI', self::$base_uri);
  }



  // ------------ PRIVATE METHODS ----------------------

  private static function load_controller()
  {
    // Get controller, class, method from URL
    $tmp = router::url_to_file(load::$config['routing']['']);

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
    if (!empty(load::$config['before_controller']))
		{
			foreach(load::$config['before_controller'] as $tmp)
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
        // Get all methods in class
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
      if (!empty(load::$config['debug']))
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