<?php

namespace Core\Controllers;

use Core\Models\Load;
use Core\Models\Config;
use Core\Models\Router;


/**
 * StaticPHP's base controller, sets various class variables and offers additional methods.
 */
class Controller
{
    public static $module_url = null;
    public static $controller_url = null;
    public static $method_url = null;


    /**
     *  Constructor - Called on each request.
     */
    public static function construct($class = null, $method = null)
    {
        // Get full urls to current controller and its method
        $site_url = Router::siteUrl();
        self::$method_url = $site_url.Router::$method_url.'/';
        self::$controller_url = dirname(self::$method_url).'/';
        self::$module_url = Router::siteUrl(strtolower(preg_replace('/(.)([A-Z])/', '$1-$2', Router::$module))).'/';

        // Pass these to the view, too
        Config::$items['view_data']['module_url'] = self::$module_url;
        Config::$items['view_data']['controller_url'] = self::$controller_url;
        Config::$items['view_data']['method_url'] = self::$method_url;

        // Add Router's preferences
        Config::$items['view_data']['module'] = Router::$module;
        Config::$items['view_data']['controller'] = Router::$controller;
        Config::$items['view_data']['class'] = Router::$class;
        Config::$items['view_data']['method'] = Router::$method;
    }


    /**
     *  Destructor - Called on each request after data is sent to browser.
     */
    public static function destruct()
    {
        // Not implemented
    }


    /**
     *  Render a view. This method instead of Load::view() prefixes paths with current module directory.
     */
    public static function render($views, $data = [])
    {
        $views = (array)$views;
        foreach ($views as $key => $item) {
            $views[$key] = Router::$module.DS.'Views'.DS.$item;
        }
        Load::view($views, $data);
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
