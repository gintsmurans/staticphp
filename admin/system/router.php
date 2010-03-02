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

  public static $lang_current = NULL;

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





// ------------ PRIVATE METHODS ----------------------


  private static function split_segments()
  {
    global $config;

    // Get some config variables
    $uri = urldecode($config->request_uri);
    $script_path = self::trim_slashes(dirname($config->script_name), true);

    // Set some variables
    self::$domain_uri = 'http'.(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/';
    self::$base_uri = ($config->base_uri === 'auto' ? self::$domain_uri . (!empty($script_path) ? $script_path.'/' : '') : $config->base_uri);

    // Replace script_path in uri
    $uri = self::trim_slashes(preg_replace('/^\/?'.preg_quote($script_path, '/').'/', '', $uri), true);

    // Set segments_full_uri
    self::$segments_full_uri = $uri . (empty($config->query_string) ? '' : '?'. $config->query_string);

    // Explode segments
    self::$segments = self::$segments_full = explode('/', $uri);


    // Get URI prefixes
    foreach($config->uri_prefixes as $item)
    {
      if (isset(self::$segments[0]) && self::$segments[0] == $item)
      {
        array_shift(self::$segments);
        self::$prefixes[$item] = $item;
      }
    }
    self::$prefixes_uri = implode('/', self::$prefixes);


    // Language support
    if ($config->lang_support === true)
    {
      // Set current country as first from array
      self::$lang_current = &reset($config->lang_available);

      // Search for current country
      if (!empty($config->lang_key))
      {
        foreach ($config->lang_available as &$item)
        {
          if (preg_match('/'. $item['key'] .'/', $config->lang_key))
          {
            self::$lang_current = &$item;
            break;
          }
        }
      }
      
      // Set current language as the first one
      self::$lang_current['current'] = self::$lang_current['languages'][0];

      // Search for current language
      if (!empty(self::$segments[0]) && in_array(self::$segments[0], self::$lang_current['languages']))
      {
        self::$lang_current['current'] = self::$segments[0];
        array_shift(self::$segments);
      }
      else
      {
        if ($config->lang_redirect === true)
        {
          self::redirect(site_url(self::$segments_full_uri), false, true);
        }
      }

      // Autoload language files from config
      foreach($config->load_languages as $tmp)
      {
        load_lang($tmp);
      }
    }


    // Set URI
    self::$segments_uri = implode('/', self::$segments);


    // Set global template variables
    g('vars')->base_url = &self::$base_uri;


    // Unset local variables
    unset($uri, $script_path, $tmp);
  }


  private static function load_controller()
  {
    // Get routing settings
    $routing = g('config')->routing;

    // Get controller, class, method from URI
    $tmp = router::uri_to_file($routing['']);

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
      // Check config routing array
      foreach((array)$routing as $key=>$item)
      {
        if (!empty($key) && !empty($item))
        {
          $key = str_replace('/', '\\/', $key);
          if (preg_match('/'.$key.'/', self::$segments_uri))
          {
            // Get controller, class, method from URI
            $tmp = router::uri_to_file($item);

            // Set file, class and method
            self::$class = $tmp['class'];
            self::$method = $tmp['method'];
            self::$file = $tmp['file'];
          }
        }
      }


      // If there was no corresponding records from routing array, try segments
      if (empty(self::$file))
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
    }

    // Load pre controllers hook
    //load_hook('pre_controller');

    // Load controllers
    self::_load_controller(APP_PATH .'controllers' . DS . self::$file.'.php', self::$class, self::$method);

    // Load post controllers hook
    //load_hook('post_controller');

    // Unset
    unset($tmp, $mi, $routing);
  }



  public static function _load_controller($File, $Class, $Method)
  {
    // Check for $File
    if (is_file($File))
    {
      include($File);

      // Check for $Class
      if (class_exists($Class))
      {
        $methods = get_class_methods($Class);
        // Check for $Method
        if (in_array($Method, $methods) || in_array('__callStatic', $methods))
        {
          // Call our contructor
          if (in_array('_construct', $methods))
          {
            call_user_func(array($Class, '_construct'), $Class, $Method);
          }
          call_user_func(array($Class, $Method));
        }
        else
        {
          $error = 'Class was found, but could not find method';
        }
      }
      else
      {
        $error = 'File was included, but could not find class';
      }
    }
    else
    {
      $error = 'Controller file was not found';
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