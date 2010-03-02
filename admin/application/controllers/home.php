<?php

class home
{

  public static function _construct()
  {

  }
  
  public static function index()
  {
    user_model::check_access();

    load('views/header');
    load('views/footer');
  }
  
  public static function base_js()
  {
    header('Content-Type: text/javascript');
    load('views/base.js');
  }
}

?>