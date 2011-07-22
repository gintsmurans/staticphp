<?php

class home
{
  # Variables to pass to a view, I like to keep them in global array
  private static $data = array();


  # This is called every time controller loads
  public static function _construct()
  {

  }


  # Default method
  public static function index()
  {
    // Enable timer output
    load::set('timer', TRUE);

    // Same as
    load::$config['timer'] = TRUE;
    
    // Turn on debugging to display timer output
    load::$config['debug'] = TRUE;

    // Load view
    // Pass array (key => value) as second parameter, to get variables available in your view
    load::view(array('header', 'home/index', 'footer'), self::$data);
  }
}

?>