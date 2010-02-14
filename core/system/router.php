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
  public static $url = null;
  public static $full_url = null;
  public static $prefixes = array();
  public static $segments = array();

  public static $file = null;
  public static $class = null;
  public static $method = null;


  public static function init()
  {
    self::parse_url();
    self::split_segments();
    self::load_controller();
  }


  public static function redirect($url = '', $site_url = true, $e301 = false, $type = 'http')
  {
    switch ($type)
    {
      case 'js':
        echo '<script type="text/javascript"> window.location.href = \''.($site_url === false ? $url : site_url($url)).'\'; </script>';
      break;
      
      default:
        if ($e301 === true)
        {
            header("HTTP/1.1 301 Moved Permanently");
        }
        
        header("Location: ".($site_url === false ? $url : site_url($url)));
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


  public static function url_to_file($url)
  {
    // Explode $url
    $tmp = explode('/', $url);

    // Get class, method and file from $url
    $data['method'] = array_pop($tmp);
    $data['class'] = end($tmp);
    $data['file'] = implode('/', $tmp);

    // Unset $tmp and return array
    unset($tmp);
    return $data;
  }





// ------------ PRIVATE METHODS ----------------------

  
  private static function parse_url()
  {
    // Get urls
    $domain_url = 'http'.(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/';
    $script_path = self::trim_slashes(dirname($_SERVER['SCRIPT_NAME']), true);

    // Get request string        
    self::$url = urldecode(g('config')->request_uri);

    // Replace directory in url
    self::$url = self::trim_slashes(preg_replace('/^\/?'.preg_quote($script_path, '/').'|\?.*/', '', self::$url), true);
    self::$full_url = self::$url . (empty(g('config')->query_string) ? '' : '?'. g('config')->query_string);

    // Set config
    g('config')->domain_url = $domain_url;
    g('config')->base_url = (g('config')->base_url === 'auto' ? $domain_url.(!empty($script_path) ? $script_path.'/' : '') : g('config')->base_url);
    g('vars')->base_url =& g('config')->base_url;
  }


  private static function split_segments()
  {
    self::$segments = explode('/', self::$url);

    // Get URL prefixes
    foreach(g('config')->url_prefixes as $item)
    {
      if (isset(self::$segments[0]) && self::$segments[0] == $item)
      {
        array_shift(self::$segments);
        self::$prefixes[$item] = '';
      }
    }

    // Language support
    if (g('config')->lang_support === true)
    {
      if (!empty(self::$segments[0]) && in_array(self::$segments[0], g('config')->languages))
      {
        g('config')->language = self::$segments[0];
        array_shift(self::$segments);
      }
      else
      {
        if (g('config')->lang_redirect === true)
        {
          self::redirect(site_url(g('config')->lang_default . '/' . implode('/', self::$segments), implode('/', self::$prefixes), false), false, true);
        }
        else
        {
          g('config')->language =& g('config')->lang_default;
        }
      }

      // Autoload language files from config
      foreach(g('config')->load_languages as $tmp)
      {
        load_lang($tmp);
      }
    }
  }


  private static function load_controller()
  {
    // Get routing settings
    $routing = g('config')->routing;

    // Get controller, class, method from url
    $tmp = router::url_to_file($routing['']);

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
          if (preg_match('/'.$key.'/', self::$url))
          {
            // Get controller, class, method from url
            $tmp = router::url_to_file($item);

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
    if (!empty(g('config')->hooks['pre_controller']))
    {
      load_hook(g('config')->hooks['pre_controller']);
    }

    // Load controllers
    self::_load_controller(APP_PATH .'controllers' . DS . self::$file.'.php', self::$class, self::$method);

    // Load post controllers hook
    if (!empty(g('config')->hooks['post_controller']))
    {
      load_hook(g('config')->hooks['post_controller']);
    }

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