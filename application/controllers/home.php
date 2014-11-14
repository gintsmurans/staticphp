<?php

namespace controllers;

use \core\load;
use \core\router;

class home
{
    # This is called every time controller loads
    public static function construct()
    {
    }
    
    # Default method
    public static function index()
    {
        // Do something heavy and add timer mark
        load::markTime('Before views');

        // Load view
        // Pass [key => value] as second parameter, to get variables available in your view
        load::view('home/index.html', $data);
    }
}

?>