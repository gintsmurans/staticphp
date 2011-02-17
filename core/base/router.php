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
    return router::$base_uri . $url;
  }


  # Get site uri
  public static function site_uri($url = '', $prefix = NULL, $current_prefix = TRUE)
  {
    $url002  = !empty($prefix) ? trim($prefix, '/') . '/' : '';
    $url002 .= !empty($current_prefix) && !empty(router::$prefixes_url) ? router::$prefixes_url . '/' : '';
    return router::$base_uri . $url002 . $url;
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
        echo '<script type="text/javascript"> window.location.href = \'', ($site_uri === FALSE ? $url : router::site_uri($url)), '\'; </script>';
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


  # Return segment in url by $index
  public static function segment($index)
  {
    return (empty(self::$segments[$index]) ? NULL : self::$segments[$index]);
  }


  # Show http error
  public static function error($error_code, $error_string)
  {
    header('HTTP/1.0 '. $error_code .' '. $error_string);
    load::view('errors/E'. $error_code);
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
    $script_path = trim(dirname(load::$config['script_name']), '/');

    // Set some variables
    self::$domain_uri = 'http'.(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/';
    self::$base_uri = (empty(load::$config['base_uri']) ? self::$domain_uri . (!empty($script_path) ? $script_path.'/' : '') : load::$config['base_uri']);

    // Replace script_path in uri and remove query string
    $uri = trim(empty($script_path) ? $uri : str_replace('/' . $script_path, '', $uri), '/');
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

		switch (TRUE)
		{
			case (!empty(self::$segments[1]) && is_file(BASE_PATH .'controllers'. DS . self::$segments[0] . DS . self::$segments[1] . '.php')):
				self::$class = self::$segments[1];
				self::$file = self::$segments[0] . DS . self::$segments[1];
				if (!empty(self::$segments[2]))
				{
					self::$method = self::$segments[2];
				}
			break;
			
			case (!empty(self::$segments[0])):
				self::$class = self::$segments[0];
				self::$file = self::$segments[0];
				if (!is_file(BASE_PATH .'controllers'. DS . self::$segments[0] . '.php'))
				{
					self::$file .= DS . self::$segments[0];
				}
				if (!empty(self::$segments[1]))
				{
					self::$method = self::$segments[1];
				}
			break;
			
			default:
				self::$file = $tmp['file'];
			break;
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
    self::_load_controller(BASE_PATH .'controllers'. DS . self::$file .'.php', self::$class, self::$method);
  }



  public static function _load_controller($file, $class, $method)
  {
    // Check for $File
    if (is_file($file))
    {
      include $file;

      // Get all methods in class
      if (is_array($methods = get_class_methods($class)))
      {
        $methods = array_flip($methods);
      }

      // Call our contructor
      if (isset($methods['_construct']))
      {
        call_user_func(array($class, '_construct'), $class, $method);
        # $class::_construct($class, $method);
      }

      // Check for $Method
      if (isset($methods[$method]) || isset($methods['__callStatic']))
      {
        call_user_func_array(array($class, $method), self::$segments);
        # $class::$method();
      }
      else
      {
        $error = 'Class or method could not be found: ' . $method;
      }
    }
    else
    {
      $error = 'Controller file was not found: ' . $file;
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