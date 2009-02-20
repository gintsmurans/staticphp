<?php


class home
{

    // Variables to pass to a view, I like to keep them in global array
    private static $vars = array();
    

    // This is called every time controller loads    
    public static function construct()
    {
    }


    // Default method
    public static function index()
    {
        // Enable timer output
        g('config')->timer = true;

        // Load view
        // Pass array (key => value) as second parameter, to get variables available in your view
        load('views/home', self::$vars);
    }
    
    
}

?>