<?php
/*
  "StaticPHP Framework" - Little PHP Framework

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

  Copyright (C) 2009  Gints Murāns <gm@mstuff.org>
*/


class router
{

    public static $url = null;
    public static $full_url = null;
    public static $prefixes = null;
    public static $segments = null;

    public static $file = null;    
    public static $class = null;
    public static $method = null;


    public static function init()
    {
        self::parse_url();
        self::split_segments();
        self::load_controller();
    }


    public static function redirect($url = '', $site_url = true, $e301 = false)
    {
        if ($e301 === true)
        {
            header("HTTP/1.1 301 Moved Permanently");
        }
        
        header("Location: ".($site_url === false ? $url : site_url($url)));
        header("Connection: close");

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






// ------------ PRIVATE METHODS ----------------------

    
    private static function parse_url()
    {
        // Get urls
        $domain_url = 'http'.(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/';
        $script_path = self::trim_slashes((g('config')->base_url === 'auto' ? dirname($_SERVER['SCRIPT_NAME']) : g('config')->base_url), true);
        
        // Get request string        
        self::$url = urldecode(g('config')->request_uri);
        
        // Replace directory in url
        self::$url = self::trim_slashes(preg_replace('/^\/?'.preg_quote($script_path, '/').'|\?.*/', '', self::$url), true);
        self::$full_url = self::$url . (empty(g('config')->query_string) ? '' : '?'. g('config')->query_string);

        // Set config
        g('config')->domain_url = $domain_url;
        g('config')->base_url = $domain_url.(!empty($script_path) ? $script_path.'/' : '');
    }
    
    
    private static function split_segments()
    {
        $prefixes = array();
        $lang_prefixes = array_keys(g('config')->languages);
        $segments = explode('/', self::$url);
        
        
        // Get URL prefixes
        foreach(g('config')->url_prefixes as $item)
        {
            if (isset($segments[0]) && $segments[0] == $item)
            {
                array_shift($segments);

                $prefixes[$item] = '';
            }
        }


        // Get language
        if (empty($segments[0]) || !in_array($segments[0], $lang_prefixes))
        {
            if (g('config')->lang_redirect === true)
            {
                self::redirect(site_url(g('config')->lang_default_prefix . '/' . implode('/', $segments), implode('/', $prefixes), false), false, true);
            }
            else
            {
                $lang = g('config')->lang_default_prefix;
            }
        }
        else
        {
            $lang = $segments[0];
            array_shift($segments);
        }


        // Set language
        g('config')->language = $lang;

        // Set segments and prefixes
        self::$segments = $segments;
        self::$prefixes = $prefixes;


        // Unset local ones
        unset($segments, $prefixes, $lang);
    }


    private static function load_controller()
    {
        // Get routing settings
        $routing = g('config')->routing;
        
        // Get controllers path
        $cpath = (defined('ADMIN_PATH') ? ADMIN_PATH : APP_PATH);
        
		// Explode default method
		$tmp = explode('/', $routing['']);
		$count = count($tmp);
		
		// Set default class and method
		self::$class = $tmp[$count - 2];
        self::$method = $tmp[$count - 1];



        // If empty segments set file as class name
        if (empty(self::$segments[0]))
        {
        	self::$file = implode('/', array_slice($tmp, 0, -1));
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
                        // Explode found segments
	                    $tmp = explode('/', $item);
	                    $count = count($tmp);

                        // Set file, class and method
                        self::$file = implode('/', array_slice($tmp, 0, -1));
                        self::$class = $tmp[$count - 2];
                        self::$method = $tmp[$count - 1];

	                    unset($tmp);
	                }
	            }
	        }
	
	
	        // If there was no corresponding records from routing array, try segments
	        if (empty(self::$file))
	        {
            	self::$file = self::$segments[0];
    			$mi = 1;

                // Check for subdirectory
                if (is_dir($cpath.'controllers'.DS.self::$file))
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


		self::_load_controller($cpath.'controllers'.DS.self::$file.'.php', self::$class, self::$method);
        unset($mi, $routing);
    }
    
    
    
    public static function _load_controller($File, $Class, $Method)
    {
      // Check for controller file and class name
      if (is_file($File))
      {
        include_once($File);
        if (class_exists($Class))
        {
          $methods = get_class_methods($Class);
          if (in_array($Method, $methods) || in_array('__callStatic', $methods))
          {
              // Call our contructor
              if (in_array('__construct__', $methods))
              {
                  call_user_func(array($Class, '__construct__'));
              }
              call_user_func(array($Class, $Method));
          }
          else
          {
          	self::error('404', 'Not Found');
          }
        }
        else
        {
          throw new Exception('Can\'t load controller');
        }
      }
      else
      {
        self::error('404', 'Not Found');
      }

      unset($methods);
    }

}

?>