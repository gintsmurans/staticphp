<?php


class home
{
    private static $vars = array();


    public static function index()
    {
        router::redirect('language'); 
    }
}


?>