<?php

namespace core;


class router
{

    /*
    |--------------------------------------------------------------------------
    | Variables
    |--------------------------------------------------------------------------
    */

    public static $prefixes = [];
    public static $prefixes_url = null;

    public static $segments = [];
    public static $segments_url = null;
    public static $segments_full_url = null;
    public static $segments_requested = [];

    public static $domain_uri = null;
    public static $base_url = null;

    public static $file = null;
    public static $class = null;
    public static $method = null;


    /*
    |--------------------------------------------------------------------------
    | Helper methods
    |--------------------------------------------------------------------------
    */

    # Get base uri
    public static function baseUrl($url = '')
    {
        return self::$base_url . $url;
    }


    # Get site uri
    public static function siteUrl($url = '', $prefix = null, $current_prefix = true)
    {
        $url002  = !empty($prefix) ? trim($prefix, '/') . '/' : '';
        $url002 .= !empty($current_prefix) && !empty(self::$prefixes_url) ? self::$prefixes_url . '/' : '';
        return self::$base_url . $url002 . $url;
    }


    # Redirect
    public static function redirect($url = '', $site_uri = true, $e301 = false, $type = 'http')
    {
        switch ($type)
        {
            case 'js':
                echo '<script type="text/javascript"> window.location.href = \'', ($site_uri === false ? $url : self::siteUrl($url)), '\'; </script>';
                break;

            default:
                if ($e301 === true)
                {
                    header("HTTP/1.1 301 Moved Permanently");
                }

                header("Location: ".(empty($site_uri) ? $url : self::siteUrl($url)));
                header("Connection: close");
                break;
        }
        exit;
    }


    # Have prefix
    public static function hasPrefix($p)
    {
        return (isset(self::$prefixes[$p]));
    }


    # Return segment in url by $index
    public static function segment($index)
    {
        return (empty(self::$segments[$index]) ? null : self::$segments[$index]);
    }


    # Show http error
    public static function error($error_code, $error_string = '', $description = '')
    {
        header('HTTP/1.0 '. $error_code .' '. $error_string);
        $data = ['description' => $description];
        load::view("errors/E{$error_code}.html", $data);
        exit;
    }




    /*
    |--------------------------------------------------------------------------
    | Class helper methods
    |--------------------------------------------------------------------------
    */

    # Convert / and \ to systems directory separator
    protected static function makePathString($string)
    {
        return str_replace(['/', '\\'], DS, $string);
    }


    # Convert url to file path
    protected static function urlToFile($url)
    {
        // Explode $url
        $tmp = explode('/', $url);

        // Get class, method and file from $url
        $data['method'] = array_pop($tmp);
        $data['class'] = end($tmp);
        $data['file'] = implode('/', $tmp);
        return $data;
    }




    /*
    |--------------------------------------------------------------------------
    | Router initialization methods
    |--------------------------------------------------------------------------
    */

    # Init router
    public static function init()
    {
        self::splitSegments();
        self::findController();
        self::loadController();
    }


    # Split segments
    public static function splitSegments($force = false)
    {
        if (empty($force) && !empty(self::$domain_uri))
        {
            return;
        }

        // Get some config variables
        $uri = load::$config['request_uri'];
        $script_path = trim(dirname(load::$config['script_name']), '/');
        self::$base_url = load::$config['base_url'];

        // Set some variables
        if (empty(self::$base_url) && !empty($_SERVER['HTTP_HOST']))
        {
            $https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on');
            self::$domain_uri = 'http'.(empty($https) ? '' : 's') .'://'. $_SERVER['HTTP_HOST'];
            if (strpos($_SERVER['HTTP_HOST'], ':'.$_SERVER['SERVER_PORT']) === false)
            {
                if ((empty($https) && $_SERVER['SERVER_PORT'] != 80) || (!empty($https) && $_SERVER['SERVER_PORT'] != 443))
                {
                    self::$domain_uri .= ':' . $_SERVER['SERVER_PORT'];
                }
            }
            self::$domain_uri .= '/';
            self::$base_url = self::$domain_uri . (!empty($script_path) ? $script_path . '/' : '');
        }

        // Replace script_path in uri and remove query string
        $uri = trim(empty($script_path) ? $uri : str_replace('/' . $script_path, '', $uri), '/');
        $uri = preg_replace('/\?.*/', '', $uri);

        // Check config routing array
        $uri_tmp = $uri;
        foreach(load::$config['routing'] as $key => &$item)
        {
            if (!empty($key) && !empty($item))
            {
                $key = str_replace('#', '\\#', $key);
                $tmp = preg_replace('#'.$key.'#', $item, $uri);
                if ($tmp !== $uri)
                {
                    self::$segments_requested = explode('/', $uri);
                    $uri_tmp = $tmp;
                }
            }
        }
        $uri = $uri_tmp;

        // Set segments_full_url
        self::$segments_full_url = $uri . (empty(load::$config['query_string']) ? '' : '?'. load::$config['query_string']);

        // Explode segments
        self::$segments = (empty($uri) ? [] : explode('/', $uri));
        self::$segments = array_map('rawurldecode', self::$segments);

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

        // Define base_url
        define('BASE_URL', self::$base_url);
    }



    /*
    |--------------------------------------------------------------------------
    | Controller loading
    |--------------------------------------------------------------------------
    */

    public static function findController()
    {
        // Get default controller, class, method from URL
        $tmp = self::urlToFile(load::$config['routing']['']);

        // Set default class and method
        self::$class = $tmp['class'];
        self::$method = $tmp['method'];

        // Controller and method count, this number is needed because of subdirectory controllers and possibility to have and have not method provided
        $count = 0;

        switch (true)
        {
            // Controller is in subdirectory
            case (!empty(self::$segments[1]) && is_file(APP_PATH .'controllers'. DS . self::$segments[0] . DS . self::$segments[1] . '.php')):
                $count = 2;
                self::$class = self::$segments[0] . '\\' . self::$segments[1];
                self::$file = self::$segments[0] . DS . self::$segments[1];
                if (!empty(self::$segments[2]))
                {
                    $count = 3;
                    self::$method = self::$segments[2];
                }
                break;

            // Controller is not in subdirectory
            case (!empty(self::$segments[0])):
                $count = 1;
                self::$class = self::$segments[0];
                self::$file = self::$segments[0];
                if (!is_file(APP_PATH .'controllers'. DS . self::$segments[0] . '.php'))
                {
                    self::$file .= DS . self::$segments[0];
                }
                if (!empty(self::$segments[1]))
                {
                    $count = 2;
                    self::$method = self::$segments[1];
                }
                break;

            // Run default controller
            default:
                self::$file = $tmp['file'];
                break;
        }

        // Remove controller and method from segments
        array_splice(self::$segments, 0, $count);
    }



    protected static function loadController($file = null, $class = null, &$method = null)
    {
        // Load current file if empty $file parameter
        if (empty($file))
        {
            $file = APP_PATH .'controllers'. DS . self::$file .'.php';
        }

        // Load current class if empty $class parameter
        if (empty($class))
        {
            $class = self::$class;
        }

        // Load current method if empty $method parameter
        if (empty($method))
        {
            $method = self::$method;
        }


        // Load pre controller hook
        if (!empty(load::$config['before_controller']))
        {
            foreach(load::$config['before_controller'] as $tmp)
            {
                call_user_func_array($tmp, [&$file, &$class, &$method]);
            }
        }


        // Check for $file
        if (is_file($file))
        {
            require $file;

            // Namespaces support
            $class = '\\controllers\\' . $class;

            // Get all methods in class
            if (is_array($methods = get_class_methods($class)))
            {
                $methods = array_flip($methods);
            }

            // Call our contructor
            if (isset($methods['construct']))
            {
                $class::construct($class, $method);
            }

            // Check for $method
            if (isset($methods[$method]) || isset($methods['__callStatic']))
            {
                call_user_func_array([$class, $method], self::$segments);
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
                self::error('500', 'Internal Server Error', $error);
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