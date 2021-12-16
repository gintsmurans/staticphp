<?php

namespace Defaults\Controllers;

use \Core\Controllers\Controller;
use \Core\Models\Load;
use \Core\Models\Timers;

/**
 * Welcome page controller.
 */

class Welcome extends Controller
{
    /**
     * A method to be called on every call, regardless of current request handler method.
     *
     * This is a reserved method. Loading a handler with this method will result in 500 error.<br />
     * class and method that will be called can be overriden by settings $class and $method to something different.
     *
     * @access public
     * @static
     * @param  string $class
     * @param  string $method
     * @return void
     */
    public static function construct($class = null, $method = null)
    {
        // (Optionally) Call parent construct for view rendering and
        // access to self::$controller_url and self::$controller_url variables
        parent::construct($class, $method);
    }

    /**
     * Example method.
     *
     * Segments after /home/index/ will be passed as parameters in handler method.
     * For example http://example.com/home/index/post/34 will call index method from home controller with
     * $param1 == 'post' and $param2 == '34'.<br />If you would like to see a better looking urls like http://example.com/post/34
     * and home/index as handler, take a look in config/routing.php file.
     *
     * @access public
     * @static
     * @param  string $param1 (default: null)
     * @param  string $param2 (default: null)
     * @return void
     */
    public static function index($param1 = null, $param2 = null)
    {
        // Do something heavy and add timer mark
        Timers::markTime('Before views');

        // Load view
        // Pass [key => value] as second parameter, to get variables available in your view
        self::render('index.html');

        // Or call Load::view('Defaults/Views/index.html');
    }

    /**
     * Example method for example page.
     *
     * @access public
     * @static
     * @return void
     */
    public static function example()
    {
        $view_data = [
            'included_files' => []
        ];

        foreach (get_included_files() as $file) {
            $view_data['included_files'][] = $file;
        }

        Load::view('Defaults/Views/example.html', $view_data);
    }
}
