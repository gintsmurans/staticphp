<?php

class users
{
  private static $vars = array();

  public static function __construct__()
  {
    user_model::check_access();
  }
  
  
  public static function index()
  {
    self::$vars['users'] = db::query("SELECT * FROM `users` ORDER BY `username`")->fetchAll();

    load('views/header');
    load('views/users/users', self::$vars);
    load('views/footer');
  }


  public static function add()
  {
    // Generate access list
    $access =& self::$vars['access'];
    foreach (g('config')->access as $item)
    {
      $tmp =& $access[];
      $tmp['name'] = $item;
      
      if (is_file(APP_PATH .'controllers/'. $item .'.php'))
      {
        include_once APP_PATH .'controllers/'. $item .'.php';
        $tmp['methods'] = get_class_methods($item);
      }
    }


    // Post
    if (fv::ispost(array('username', 'password')))
    {
      $data = array(
        $_POST['username'],
        sha1($_POST['password']),
        (empty($_POST['c']) ? '' : json_encode($_POST['c']))
      );
      db::exec("INSERT INTO `users` SET `username` = ?, `password` = ?, `access` = ?", $data);
      router::redirect('users');
    }

    // Load views
    load('views/header');
    load('views/users/add_user', self::$vars);
    load('views/footer');
  }
  
  
  public static function edit()
  {
    // Redirect away, if user id not in segments
    if (empty(router::$segments[2]))
    {
      router::redirect('users');
    }

    // Get user
    self::$vars['user'] = db::query("SELECT * FROM `users` WHERE `id` = ? LIMIT 1", router::$segments[2])->fetch();
    self::$vars['user']->access = json_decode(self::$vars['user']->access);

    // Generate access list
    $access =& self::$vars['access'];
    foreach (g('config')->access as $item)
    {
      $tmp =& $access[];
      $tmp['name'] = $item;
      
      if (is_file(APP_PATH .'controllers/'. $item .'.php'))
      {
        include_once APP_PATH .'controllers/'. $item .'.php';
        $tmp['methods'] = get_class_methods($item);
      }
    }

    // Post
    if (fv::ispost(array('username', 'password')))
    {
      $set = '';
      $data = array(
        $_POST['username'],
        (empty($_POST['c']) ? '' : json_encode($_POST['c'])),
      );

      // If not empty password, set it too
      if (!empty($_POST['password']))
      {
        $set = ', `password` = ?';
        $data[] = sha1($_POST['password']);
      }
      $data[] = router::$segments[2];
      
      db::exec("UPDATE `users` SET `username` = ?, `access` = ?{$set} WHERE `id` = ? LIMIT 1", $data);
      router::redirect('users');
    }

    // Load views
    load('views/header');
    load('views/users/edit_user', self::$vars);
    load('views/footer');
  }
  
  
  public static function delete()
  {
    // Redirect away, if user id not in segments
    if (empty(router::$segments[2]))
    {
      router::redirect('users');
    }

    // Delete user
    self::$vars['user'] = db::query("DELETE FROM `users` WHERE `id` = ?", router::$segments[2]);
    router::redirect('users');
  }
}

?>