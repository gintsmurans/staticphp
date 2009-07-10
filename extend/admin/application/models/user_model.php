<?php

class user_model
{
  public static function check_access()
  {
    if (empty($_SESSION['user']))
    {
      $_SESSION['login_redirect'] = site_url(router::$full_url);
      router::redirect('login');
    }

    if (
      (empty($_SESSION['user']->access->{'*'}) || $_SESSION['user']->access->{'*'} != '*' ) && 
      (empty($_SESSION['user']->access->{router::$class}) || $_SESSION['user']->access->{router::$class} != '*') && 
      (empty($_SESSION['user']->access->{router::$class}->{router::$method}))
    )
    {
      router::error(403, 'Forbidden');
    }
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