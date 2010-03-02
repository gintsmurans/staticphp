<?php


class login
{


  private static $vars = array();

  public static function _construct()
  {
    load_lang('admin_login');
  }
  
  
  public static function index()
  {
    // Unset current session
    unset($_SESSION['user']);
    
    js(base_url('js/login.js'));
    css(base_url('css/login.css'));

    load('views/login/header');
    load('views/login/login', self::$vars);
    load('views/login/footer');
  }


  public static function post()
  {
    if (fv::ispost(array('username', 'password')))
    {
      $user = user_model::login($_POST['username'], $_POST['password']);
      if (is_object($user))
      {
        $_SESSION['user'] = $user;
        $output = array('redirect' => (empty($_SESSION['login_redirect']) ? '' : $_SESSION['login_redirect']));
      }
      else
      {
        $output = array('error' => $user); 
      }

      echo json_encode($output);
    }
  }
  
  public static function out()
  {
    unset($_SESSION['user']);
    router::redirect('');
  }
}

?>