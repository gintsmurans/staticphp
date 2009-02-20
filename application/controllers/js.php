<?php

class js
{
    public static function index()
    {
    	$filename = PUBLIC_PATH.implode('/', router::$segments);
    	
        if (!empty(Router::$segments[1]) && is_file($filename))
        {
            // Disable timer output
            g('config')->timer = false;
            
            // Set header and include js file
            header('Content-type: text/javascript; charset=utf-8');
            include_once($filename);
        }
        else
        {
            router::e404();
        }
    }
}

?>