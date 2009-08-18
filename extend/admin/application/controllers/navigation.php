<?php

class navigation
{
  public static $vars = array();
  
  public static function __construct__()
  {
    // Check user access
    user_model::check_access();
    
    // Load lang
    load_lang('navigation');

    // load navigation model
    load('models/navigation_model');
    self::$vars['models'] =& navigation_model::get_models();
    navigation_model::get_menu_array();
    navigation_model::process_menu_array();
    
    // Get default method
    $routing = array_reverse(explode('/', g('config')->routing['']));
    
    g('nav')->language = (empty(router::$segments[2]) || !in_array(router::$segments[2], g('config')->languages) ? g('config')->language : router::$segments[2]);
    g('nav')->menu_id = (empty(router::$segments[3]) ? 1 : router::$segments[3]);
    g('nav')->nav_id = (empty(router::$segments[4]) || empty(navigation_model::$menu_array[router::$segments[4]]) ? 0 : router::$segments[4]);
    g('nav')->module_method = (empty(router::$segments[5]) ? $routing[0] : router::$segments[5]);

    // Get opened item
    if (!empty(g('nav')->nav_id))
    {
      self::$vars['nav_item'] =& navigation_model::$menu_array[g('nav')->nav_id];
    }

    js('inline:
      var menu_id = '. g('nav')->menu_id .';
      var parent_id = '. g('nav')->nav_id .';
      var language = \''. g('nav')->language .'\';
    ');

    css(base_url('css/navigation.css'), base_url('css/jquery.treeview.css'));
    js(base_url('js/navigation.js'), base_url('js/jquery.treeview.js'), base_url('js/jquery.cookie.js'));
  }

  public static function index()
  {
    if (!empty(g('nav')->nav_id))
    {
      $model = db::query("SELECT `name` FROM `models` WHERE `id` = ? LIMIT 1", self::$vars['nav_item']->model_id)->fetch();
      if (!empty($model->name))
      {
        load('controllers/'. $model->name);
        call_user_func(array($model->name, '__construct__'));
      }
    }

    load('views/header');
    load('views/navigation/navigation', self::$vars);

    if (!empty($model->name))
    {
      call_user_func(array($model->name, g('nav')->module_method));
    }

    load('views/navigation/footer');
    load('views/footer');
  }
  
  
  public static function add()
  {
    load('views/header');
    load('views/navigation/navigation', self::$vars);
    load('views/navigation/add', self::$vars);
    load('views/navigation/footer');
    load('views/footer');
  }
  
  
  public static function edit()
  {
    load('views/header');
    load('views/navigation/navigation', self::$vars);
    load('views/navigation/edit', self::$vars);
    load('views/navigation/footer');
    load('views/footer');
  }
  
  public static function add_new()
  {
    $menu = db::query("SELECT max(`menu_id`) as menu_id FROM `navigation`;")->fetch();
    js('inline:
      var parent_id = '. (empty(g('nav')->nav_id) ? 0 : g('nav')->nav_id) .';
      var menu_id = '. ($menu->menu_id + 1) .';
    ');

    load('views/header');
    load('views/navigation/navigation', self::$vars);
    load('views/navigation/add', self::$vars);
    load('views/footer');
  }
  
  
  public static function get_url()
  {
    if (fv::ispost('title'))
    {
      $title = fv::set_friendly($_POST['title']);
      if (empty($title))
      {
        $data['error'] = NAV_ERROR1;
      }
      else
      {
        $tmp = db::query("SELECT `url` FROM `navigation` WHERE `url` LIKE ? ORDER BY `url` DESC", array($title.'%'))->fetch();
        if (!empty($tmp))
        {
          $tmp = array_reverse(explode('-', $tmp->url));
          $tmp = (int) $tmp[0] + 1;
          $title .= '-'.$tmp;
        }

        $data['url'] = $title;
      }
      echo json_encode($data);
    }
  }
  
  
  public static function add_item()
  {
    if (fv::ispost('title', 'url', 'model'))
    {
      $sort = db::query("SELECT max(`sort`) as sort FROM `navigation` WHERE `parent_id` = ?", g('nav')->nav_id)->fetch();
      $sort = (empty($sort->sort) ? 1 : $sort->sort + 1);

      db::exec(
        "INSERT INTO `navigation` SET `menu_id` = ?, `parent_id` = ?, `language` = ?, `title` = ?, `url` = ?, `model_id` = ?, `sort` = ?, `active` = ?",
        array(
          g('nav')->menu_id,
          g('nav')->nav_id, 
          g('nav')->language, 
          $_POST['title'], 
          $_POST['url'], 
          $_POST['model'], 
          $sort, 
          (empty($_POST['active']) ? 0 : 1)
        )
      );
      $id = db::query("SELECT LAST_INSERT_ID() as id;")->fetch();
      echo json_encode(array('redirect' => site_url('navigation/index/'. g('nav')->language .'/'. g('nav')->menu_id .'/'. $id->id)));
    }
  }

  public static function edit_item()
  {
    if (fv::ispost('title', 'url', 'model'))
    {
      db::exec(
        "UPDATE `navigation` SET `title` = ?, `url` = ?, `model_id` = ?, `active` = ? WHERE `id` = ? LIMIT 1",
        array($_POST['title'], $_POST['url'], $_POST['model'], (empty($_POST['active']) ? 0 : 1), g('nav')->nav_id)
      );

      echo json_encode(array('redirect' => site_url('navigation/index/'. g('nav')->language .'/'. g('nav')->menu_id .'/'. g('nav')->nav_id)));
    }
  }
  
  
  public static function delete_item()
  {
    if (!empty(g('nav')->nav_id))
    {
      $model = db::query("SELECT `name` FROM `models` WHERE `id` = ? LIMIT 1", self::$vars['nav_item']->model_id)->fetch();
      
      if (!empty($model->name))
      {
        load('controllers/'. $model->name);
        $methods = get_class_methods($model->name);
        if (in_array('delete_nav', $methods))
        {
          call_user_func(array($model->name, 'delete_nav'), self::$vars['nav_item']->id);
        }
      }

      db::exec("DELETE FROM `navigation` WHERE `id` = ? LIMIT 1", self::$vars['nav_item']->id);
      unset(navigation_model::$processed_menu_array[g('nav')->menu_id][g('nav')->nav_id]);
    }

    router::redirect('navigation/index/'. g('nav')->language . (empty(navigation_model::$processed_menu_array[g('nav')->menu_id]) ? '' : '/'. g('nav')->menu_id));
  }
  
  public static function delete_menu()
  {
    if (!empty(g('nav')->menu_id))
    {
      // TODO
      return;
      $model = db::query("SELECT `name` FROM `models` WHERE `id` = ? LIMIT 1", self::$vars['nav_item']->model_id)->fetch();
      
      if (!empty($model->name))
      {
        load('controllers/'. $model->name);
        $methods = get_class_methods($model->name);
        if (in_array('delete_nav', $methods))
        {
          call_user_func(array($model->name, 'delete_nav'), self::$vars['nav_item']->id);
        }
      }

      db::exec("DELETE FROM `navigation` WHERE `id` = ? LIMIT 1", self::$vars['nav_item']->id);
      unset(navigation_model::$processed_menu_array[g('nav')->menu_id][g('nav')->nav_id]);
    }

    router::redirect('navigation/index/'. g('nav')->language . (empty(navigation_model::$processed_menu_array[g('nav')->menu_id]) ? '' : '/'. g('nav')->menu_id));
  }  
}

?>