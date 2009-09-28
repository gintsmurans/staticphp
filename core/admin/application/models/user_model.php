<?php

class user_model
{

  public static function generate_access_list()
  {
    foreach (g('config')->access as $item)
    {
      if (is_file(APP_PATH .'controllers/'. $item .'.php'))
      {
        include_once APP_PATH .'controllers/'. $item .'.php';
        $methods = get_class_methods($item);
        if (!empty($methods))
        {
          // Add name to the list
          $tmp =& $access_list[];
          $tmp['name'] = $item;

          // Sort methods and add to the list
          asort($methods);
          foreach ($methods as $method)
          {
            if ($method[0] != '_')
            {
              $tmp['methods'][] = $method;
            }
          }
        }
      }
    }

    return (empty($access_list) ? false : $access_list);
  }

  public static function check_access($class = '', $method = '')
  {
    $class = (empty($class) ? router::$class : $class);
    $method = (empty($method) ? router::$method : $method);

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
      return LOGIN_NO_USERNAME;
    }
    elseif (empty($password))
    {
      return LOGIN_NO_PASSWORD;
    }
    else
    {
      $user = db::query("SELECT * FROM `users` WHERE `username` = ?", array($username))->fetch();
      if (empty($user->id))
      {
        return LOGIN_USERNAME_NOT_FOUND;
      }
      elseif (sha1($_POST['password']) != $user->password)
      {
        return LOGIN_WRONG_PASSWORD;
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