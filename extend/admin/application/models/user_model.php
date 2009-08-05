<?php

class user_model
{
  public static function check_access($class = '', $method = '')
  {
    $class = (empty($class) ? router::$class : $class);
    $method = (empty($method) ? router::$class : $method);

    // Check for user
    if (empty($_SESSION['user']))
    {
      $_SESSION['login_redirect'] = site_url(router::$full_url);
      router::redirect('login');
    }

    // Check access    
    if (self::_access($class, $method) === false)
    {
      router::error(403, 'Forbidden');
      return false;
    }

    // Else return true
    return true;
  }


  public static function _access($class, $method = '')
  {
    return !(
      (empty($_SESSION['user']->access->{'*'}) || $_SESSION['user']->access->{'*'} != '*' ) && 
      (empty($_SESSION['user']->access->{$class}) || $_SESSION['user']->access->{$class} != '*') && 
      (empty($_SESSION['user']->access->{$class}->{$method}))
    );
  }


  public static function login($username, $password)
  {
    if (empty($username))
    {
      return 'No username provided';
    }
    elseif (empty($password))
    {
      return 'No password provided';
    }
    else
    {
      $user = db::query("SELECT * FROM `users` WHERE `username` = ?", array($username))->fetch();
      if (empty($user->id))
      {
        return 'Username not found!';
      }
      elseif (sha1($_POST['password']) != $user->password)
      {
        return 'Wrong password!';
      }
      else
      {
        $user->access = json_decode($user->access);
        return $user;
      }
    }
  }
}

?>