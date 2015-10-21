<?php

namespace Core\Controllers;

use Core\Models\Router;
use Core\Models\Load;

class Controller
{
    public static $module_url = null;
    public static $controller_url = null;
    public static $method_url = null;


    /**
     *  Constructor - Called on each request
     */
    public static function construct(& $class = null, & $method = null)
    {
        // Get full urls to current controller and its method
        $site_url = Router::siteUrl();
        self::$module_url = $site_url.Router::$module.'/';
        self::$controller_url = $site_url.dirname(Router::$method_url).'/';
        self::$method_url = $site_url.Router::$method_url.'/';

        // Pass these to the view, too
        Load::$config['view_data']['module_url'] = self::$module_url;
        Load::$config['view_data']['controller_url'] = self::$controller_url;
        Load::$config['view_data']['method_url'] = self::$method_url;
    }


    /**
     *  Destructor - Called on each request after data is sent to browser
     */
    public static function destruct()
    {
        // Not implemented
    }


    /**
     *  Render a view. This method instead of Load::view() prefixes paths with current module directory.
     */
    public static function render($views)
    {
        $views = (array)$views;
        foreach ($views as $key => $item) {
            $views[$key] = Router::$module.DS.'Views'.DS.$item;
        }
        Load::view($views);
    }


    /**
     *  Write $contents to the output. Arrays are jsonified.
     */
    public static function write($contents)
    {
        if (is_array($contents)) {
            echo json_encode($contents);
        } else {
            echo $contents;
        }
    }
}
