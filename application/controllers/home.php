<?php

namespace controllers;


class home
{
    # This is called every time controller loads
    public static function _construct()
    {
    
    }
    
    # Default method
    public static function index()
    {
        // Do something heavy and add timer mark
        \load::mark_time('Before views');

        // Load view
        // Pass [key => value] as second parameter, to get variables available in your view
        \load::view(['header', 'home/index', 'footer'], $data);
    }
}

?>