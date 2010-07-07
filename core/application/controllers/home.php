<?php

class home
{
  // Variables to pass to a view, I like to keep them in global array
  private static $vars = array();


  // This is called every time controller loads    
  public static function _construct()
  {

  }


  // Default method
  public static function index()
  {
    // Enable timer output
    g('config')->timer = true;

    // Load view
    // Pass array (key => value) as second parameter, to get variables available in your view
    load(array('views/header', 'views/home', 'views/footer'), self::$vars);
  }    
}

?>