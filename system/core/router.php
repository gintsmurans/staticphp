<?php

namespace core;

/**
 * Router exception class.
 *
 * Custom exception class for router exceptions to allow our exception handler to give specific output for router exceptions.
 */
class RouterException extends \Exception
{
}


/**
 * Router class.
 *
 * Handles url parsing, routing and controller loading.
 */

class router
{
    /*
    |--------------------------------------------------------------------------
    | Variables
    |--------------------------------------------------------------------------
    */

    /**
     * Array of prefixes for current request
     *
     * (default value: [])
     *
     * @var string[]
     * @access public
     * @static
     */
    public static $prefixes = [];

    /**
     * Url containing all prefixes for current request
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $prefixes_url = null;

    /**
     * Array of final url segments, i.e. everything after slash after domain name, except prefixes.
     *
     * (default value: [])
     *
     * @var string[]
     * @access public
     * @static
     */
    public static $segments = [];

    /**
     * String of url segments.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $segments_url = null;

    /**
     * String containing full url to the current request.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $segments_full_url = null;

    /**
     * Original request segments, before processing config/routing.php.
     *
     * (default value: [])
     *
     * @var string[]
     * @access public
     * @static
     */
    public static $segments_requested = [];

    /**
     * Url of protocol, hostname, domain name and port number (if its not 80 or 443 for https).
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $domain_uri = null;

    /**
     * Variable that holds reference to base url.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $base_url = null;

    /**
     * Path to controller file to be loaded.
     *
     * (default value: null)
     *
     * @var strig
     * @access public
     * @static
     */
    public static $file = null;

    /**
     * Namespace to load controller class from.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $namespace = null;

    /**
     * Class name to call controller methods from.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $class = null;

    /**
     * Controller class method to be called to handle this request.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $method = null;

    /*
    |--------------------------------------------------------------------------
    | Helper methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get base url of the website.
     *
     * Appends $url if provided.
     *
     * @access public
     * @static
     * @param  string $url (default: '')
     * @return string
     */
    public static function baseUrl($url = '')
    {
        return self::$base_url.$url;
    }

    /**
     * Get site url of the website.
     *
     * Returns baseurl + optional prefixes + original prefixes
     * (if $current_prefix is set to true) and appends $url if provided.
     *
     * @access public
     * @static
     * @param  string $url            (default: '')
     * @param  mixed  $prefix         (default: null)
     * @param  bool   $current_prefix (default: true)
     * @return string
     */
    public static function siteUrl($url = '', $prefix = null, $current_prefix = true)
    {
        $url002  = !empty($prefix) ? trim($prefix, '/').'/' : '';
        $url002 .= !empty($current_prefix) && !empty(self::$prefixes_url) ? self::$prefixes_url.'/' : '';

        return self::$base_url.$url002.$url;
    }

    /**
     * Redirect browser to another $url.
     *
     * If $site_uri is provided, $url will first be passed to load::siteUrl.
     * If $e301 is set to true, "301 Moved Permanently" header will be sent too.
     * There are two types of redirects available:
     *      + http redirect - by using http headers
     *      + js redirect - by outputing location.href = $url
     *
     * @see router::siteUrl()
     * @access public
     * @static
     * @param  string $url      (default: '')
     * @param  bool   $site_uri (default: true)
     * @param  bool   $e301     (default: false)
     * @param  string $type     (default: 'http')
     * @return void
     */
    public static function redirect($url = '', $site_uri = true, $e301 = false, $type = 'http')
    {
        switch ($type) {
            case 'js':
                echo '<script type="text/javascript"> window.location.href = \'', ($site_uri === false ? $url : self::siteUrl($url)), '\'; </script>';
                break;

            default:
                if ($e301 === true) {
                    header("HTTP/1.1 301 Moved Permanently");
                }

                header("Location: ".(empty($site_uri) ? $url : self::siteUrl($url)));
                header("Connection: close");
                break;
        }
        exit;
    }

    /**
     * Check if current request url has a prefix.
     *
     * @access public
     * @static
     * @param  string $prefix
     * @return bool
     */
    public static function hasPrefix($prefix)
    {
        return (isset(self::$prefixes[$prefix]));
    }

    /**
     * Error proof method for getting segment value by segment index.
     *
     * @example Instead of getting second index of segments like this:
     *          <code>$segment = (isset(router::$segments[1])) ? router::$segments[1] : false)</code>,
     *          you can use this method like this: <code>$segment = router::segment(1);</code>.
     * @access public
     * @static
     * @param  int    $index
     * @return string
     */
    public static function segment($index)
    {
        return (empty(self::$segments[$index]) ? null : self::$segments[$index]);
    }

    /**
     * Output an error to the browser and stop script execution.
     *
     * @access public
     * @static
     * @param  int    $error_code
     * @param  string $error_string (default: '')
     * @param  string $description  (default: '')
     * @return void
     */
    public static function error($error_code, $error_string = '', $description = '')
    {
        header('HTTP/1.0 '.$error_code.' '.$error_string);
        $data = ['description' => $description];
        load::view("errors/E{$error_code}.html", $data);
        exit;
    }


    /**
     * Ease sending JSON response back to browser
     *
     * @example Call function: <code>router::jsonResponse($json_data);</code> add some data: <code>$json_data['xx'] = 1;</code>
     *          and on the end of script execution the $json_data array will be sent to client along with
     *          content-type:text/javascript header.
     * @access public
     * @param mixed &$json_data
     * @return void
     */
    public static function jsonResponse(&$json_data)
    {
        static $json_request = false;

        if (isset($GLOBALS['json_response_data']) && !empty($json_data) && is_array($json_data)) {
            $json_data = array_merge($GLOBALS['json_response_data'], $json_data);
            $GLOBALS['json_response_data'] = & $json_data;
        } elseif (isset($GLOBALS['json_response_data'])) {
            $json_data = $GLOBALS['json_response_data'];
            $GLOBALS['json_response_data'] = & $json_data;
        } elseif (empty($json_data) || is_array($json_data) == false) {
            $json_data = [];
            $GLOBALS['json_response_data'] = & $json_data;
        } else {
            $GLOBALS['json_response_data'] = & $json_data;
        }

        // Register shutdown function once
        if (empty($json_request)) {
            header('Content-Type:text/javascript; charset=utf-8');
            register_shutdown_function(function () {
                $data = $GLOBALS['json_response_data'];
                if (is_array($data) == false) {
                    $data = [];
                }
                echo json_encode($data);
            });

            $json_request = true;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Class helper methods
    |--------------------------------------------------------------------------
    */

    /**
     * Convert / and \ to host system's directory separator.
     *
     * @access protected
     * @static
     * @param  string $path
     * @return void
     */
    protected static function makePathString($path)
    {
        return str_replace(['/', '\\'], DS, $path);
    }

    /**
     * Parse url to find file, class and method to be loaded as controller.
     *
     * @access protected
     * @static
     * @param  string $url
     * @return array
     *                    An array of string objects:
     *                    <ul>
     *                    <li>'method' - method to be called</li>
     *                    <li>'class' - class where to call this method from</li>
     *                    <li>'file' - file where this class is from</li>
     *                    </ul>
     */
    protected static function urlToFile($url)
    {
        // Explode $url
        $tmp = explode('/', $url);

        // Get class, method and file from $url
        $data['method'] = array_pop($tmp);
        $data['class']  = end($tmp);
        $data['file']   = implode('/', $tmp);

        return $data;
    }

    /**
     * Fix method names to allow "-" in urls.
     *
     * @access protected
     * @static
     * @param  string $method
     * @return string
     */
    protected static function fixMethodName($method)
    {
        return lcfirst(implode('', array_map('ucfirst', explode('-', $method))));
    }


    /*
    |--------------------------------------------------------------------------
    | Router initialization methods
    |--------------------------------------------------------------------------
    */

    /**
     * Main router initialization method.
     *
     * This method calls <code>router::splitSegments();</code>, <code>router::findController()</code> and <code>router::loadController()</code> methods.
     *
     * @access public
     * @static
     * @return void
     */
    public static function init()
    {
        self::splitSegments();
        self::findController();
        self::loadController();
    }

    /**
     * Splits request url into segments.
     *
     * @access public
     * @static
     * @param  bool $force (default: false)
     * @return void
     */
    public static function splitSegments($force = false)
    {
        if (empty($force) && !empty(self::$domain_uri)) {
            return;
        }

        // Get some config variables
        $uri            = load::$config['request_uri'];
        $script_path    = trim(dirname(load::$config['script_name']), '/');
        self::$base_url = load::$config['base_url'];

        // Set some variables
        if (empty(self::$base_url) && !empty($_SERVER['HTTP_HOST'])) {
            $https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on');
            self::$domain_uri = 'http'.(empty($https) ? '' : 's').'://'.$_SERVER['HTTP_HOST'];
            if (strpos($_SERVER['HTTP_HOST'], ':'.$_SERVER['SERVER_PORT']) === false) {
                if ((empty($https) && $_SERVER['SERVER_PORT'] != 80) || (!empty($https) && $_SERVER['SERVER_PORT'] != 443)) {
                    self::$domain_uri .= ':'.$_SERVER['SERVER_PORT'];
                }
            }
            self::$domain_uri .= '/';
            self::$base_url = self::$domain_uri.(!empty($script_path) ? $script_path.'/' : '');
        }

        // Replace script_path in uri and remove query string
        $uri = trim(empty($script_path) ? $uri : str_replace('/'.$script_path, '', $uri), '/');
        $uri = preg_replace('/\?.*/', '', $uri);

        // Check config routing array
        $uri_tmp = $uri;
        foreach (load::$config['routing'] as $key => &$item) {
            if (!empty($key) && !empty($item)) {
                $key = str_replace('#', '\\#', $key);
                $tmp = preg_replace('#'.$key.'#', $item, $uri);
                if ($tmp !== $uri) {
                    self::$segments_requested = explode('/', $uri);
                    $uri_tmp = $tmp;
                }
            }
        }
        $uri = $uri_tmp;

        // Set segments_full_url
        self::$segments_full_url = $uri.(empty(load::$config['query_string']) ? '' : '?'.load::$config['query_string']);

        // Explode segments
        self::$segments = (empty($uri) ? [] : explode('/', $uri));
        self::$segments = array_map('rawurldecode', self::$segments);

        // Get URL prefixes
        foreach (load::$config['url_prefixes'] as &$item) {
            if (isset(self::$segments[0]) && self::$segments[0] == $item) {
                array_shift(self::$segments);
                self::$prefixes[$item] = $item;
            }

            if (isset(self::$segments_requested[0]) && self::$segments_requested[0] == $item) {
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

    /**
     * Finds controller for current request, by segments and config/routing.php.
     *
     * @access public
     * @static
     * @return void
     */
    public static function findController()
    {
        // Get default controller, class and method
        $tmp = self::urlToFile(load::$config['routing']['']);

        // Set default class and method
        self::$namespace = '\\controllers\\';
        self::$class     = $tmp['class'];
        self::$method    = $tmp['method'];

        // Controller and method count, this number is needed because of subdirectory controllers and possibility to have and have not method provided
        $count = 0;

        // Fix segment names to translate "-" in url's to camelCase
        $segments = array_map(['\\core\\router', 'fixMethodName'], self::$segments);


        if (count($segments) === 0) {
            // Defaults
            self::$file = $tmp['file'];
        }
        else {
            // Look for controller, class and method in segments
            $count = count($segments);
            foreach ($segments as $one) {
                if (preg_match('/^[a-zA-Z][a-zA-Z0-9-_]*$/', $segments[$count - 1]) == false) {
                    $count -= 1;
                    continue;
                }
                $slice        = array_slice($segments, 0, $count);
                $filename     = implode(DS, $slice);
                $path_to_file = APP_PATH.'controllers'.DS.$filename.'.php';

                if (is_file($path_to_file)) {
                    $namespace = array_slice($segments, 0, $count - 1);
                    if (!empty($namespace)) {
                        self::$namespace .= implode('\\', $namespace) . '\\';
                    }

                    self::$class = $segments[$count - 1];
                    self::$file = implode(DS, $slice);

                    if (count($segments) > $count) {
                        self::$method = $segments[$count];
                    }
                    break;
                }

                $count -= 1;
            }

            // Method also must be removed from the segments array
            $count += 1;
        }

        if ($count > 0) {
            // Remove controller and method from segments
            array_splice(self::$segments, 0, $count);
        }
    }

    /**
     * Loads controller found in current request sesison or by passed in parameters.
     *
     * This method also calls pre-controller hook.
     *
     * @access protected
     * @static
     * @param  string $file    (default: null)
     * @param  string $class   (default: null)
     * @param  string &$method (default: null)
     * @return void
     */
    protected static function loadController($file = null, $namespace = null, $class = null, &$method = null)
    {
        // Load current file if empty $file parameter
        if (empty($file)) {
            $file = APP_PATH.'controllers'.DS.self::$file.'.php';
        }

        // Load current namespace if empty $namespace parameter
        if (empty($namespace)) {
            $namespace = self::$namespace;
        }

        // Load current class if empty $class parameter
        if (empty($class)) {
            $class = self::$class;
        }

        // Load current method if empty $method parameter
        if (empty($method)) {
            $method = self::$method;
        }

        // Load pre controller hook
        if (!empty(load::$config['before_controller'])) {
            foreach (load::$config['before_controller'] as $tmp) {
                call_user_func_array($tmp, [&$file, &$class, &$method]);
            }
        }

        // Check for $file
        if (is_file($file)) {
            // Namespaces support
            $class = $namespace.$class;

            // Create new reflection object from the controller class
            try {
                $ref = new \ReflectionClass($class);
            }
            catch (\Exception $e) {
                throw new RouterException('File "'.$file.'" was loaded, but the class '.$class.' could NOT be found');
            }

            // Call our contructor, if there is any
            $response = null;
            if ($ref->hasMethod('construct') === true) {
                $response = $ref->getMethod('construct')->invokeArgs(null, [&$class, &$method]);
            }

            // Call requested method
            $method_response = null;
            if ($ref->hasMethod($method) === true) {
                $class_method = $ref->getMethod($method);
                $method_response = $class_method->invokeArgs(null, self::$segments);
            }
            // Call __callStatic
            elseif ($ref->hasMethod('__callStatic') === true) {
                $method_response = $ref->getMethod('__callStatic')->invoke(null, $method, self::$segments);
            }
            // Error - method not found
            else {
                throw new RouterException('Method "'.$method.'" of class "'.$class.'" could not be found');
            }

            // Append method response to construct response
            if ($method_response !== null) {
                if ($response === null) {
                    $response = $method_response;
                } elseif (is_array($response)) {
                    if (is_array($method_response) == false) {
                        throw new RouterException(
                            "Construct method returns <em>\"".gettype($response)."\"</em>, ".
                            "but {$method} returns <em>\"".gettype($method_response)."\"</em>"
                        );
                    }
                    $response = array_merge($response, $method_response);
                } else {
                    $response .= $method_response;
                }
            }

            // Echo response if there was any
            if ($response !== null) {
                if (is_array($response)) {
                    header('Content-Type:text/javascript; charset=utf-8');
                    echo json_encode($response);
                } elseif (is_string($response) || is_numeric($response)) {
                    echo $response;
                }
            }

        } else {
            throw new RouterException('Controller file was not found: '.$file);
        }
    }
}
