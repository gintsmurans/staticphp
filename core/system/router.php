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

  public static $file = null;
  public static $class = null;
  public static $method = null;


  public static function init()
  {
    self::split_segments();
    self::load_controller();
  }


  public static function redirect($uri = '', $site_uri = true, $e301 = false, $type = 'http')
  {
    switch ($type)
    {
      case 'js':
        echo '<script type="text/javascript"> window.location.href = \''.($site_uri === false ? $uri : site_url($uri)).'\'; </script>';
      break;
      
      default:
        if ($e301 === true)
        {
            header("HTTP/1.1 301 Moved Permanently");
        }
        
        header("Location: ".($site_uri === false ? $uri : site_url($uri)));
        header("Connection: close");
      break;
    }
    exit;
  }
  
  
  public static function have_prefix($p)
  {
    return (isset(self::$prefixes[$p]));
  }
  
  
  public static function trim_slashes($s, $booth = false)
  {
    $s = str_replace('\\', '/', $s);
    return ($booth == true ? trim($s, '/') : ltrim($s, '/'));
  }
  
  
  public static function segment($index)
  {
    return (!empty(self::$segments[$index]) ? self::$segments[$index] : false);
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

    // Unset $tmp and return array
    unset($tmp);
    return $data;
  }



  public static function split_segments($force = false)
  {
    global $config;
    
    if ($force == false && !empty(self::$domain_uri))
    {
      return;
    }

    // Get some config variables
    $uri = urldecode($config->request_uri);
    $script_path = self::trim_slashes(dirname($config->script_name), true);

    // Set some variables
    self::$domain_uri = 'http'.(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/';
    self::$base_uri = ($config->base_uri === 'auto' ? self::$domain_uri . (!empty($script_path) ? $script_path.'/' : '') : $config->base_uri);

    // Replace script_path in uri
    $uri = self::trim_slashes(preg_replace('/^\/?'.preg_quote($script_path, '/').'/', '', $uri), true);


    // Check config routing array
    foreach($config->routing as $key => &$item)
    {
      if (!empty($key) && !empty($item))
      {
        $key = str_replace('/', '\\/', $key);
        $tmp = preg_replace('/'.$key.'/', $item, $uri);
        if ($tmp !== $uri)
        {
          $uri = $tmp;
        }
      }
    }


    // Set segments_full_uri
    self::$segments_full_uri = $uri; // . (empty($config->query_string) ? '' : '?'. $config->query_string);

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
    self::$prefixes_uri = implode('/', self::$prefixes);


    // Set URI
    self::$segments_uri = implode('/', self::$segments);


    // Set global template variables
    g()->vars['base_url'] = &self::$base_uri;


    // Unset local variables
    unset($uri, $script_path, $tmp, $item, $key);
  }



  // ------------ PRIVATE METHODS ----------------------

  private static function load_controller()
  {
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

    // Load pre controllers hook
    //load_hook('pre_controller');

    // Load controllers
    self::_load_controller(APP_PATH .'controllers' . DS . self::$file.'.php', self::$class, self::$method);

    // Load post controllers hook
    //load_hook('post_controller');

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
      if (g('config')->debug === true)
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