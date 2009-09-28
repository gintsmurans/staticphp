<?php

class texts
{

  public static $vars = array();

  public static function __construct__()
  {
    load_lang('admin_texts');

    css(base_url('css/texts.css'), base_url('css/jquery.wysiwyg.css'));
    js(base_url('js/texts.js'), base_url('js/jquery.wysiwyg.js'));
  }

  public static function index()
  {
    if (!empty(g('nav')->nav_id))
    {
      self::$vars['posts'] = db::query('SELECT * FROM `texts` WHERE `nav_id` = ? ORDER BY `sort` DESC', g('nav')->nav_id)->fetchAll();
    }

    load('views/texts/list', self::$vars);
  }
  
  public static function get_url()
  {
    if (fv::ispost('title'))
    {
      $title = fv::set_friendly($_POST['title']);
      if (empty($title))
      {
        $data['error'] = TEXTS_ERROR1;
      }
      else
      {
        $tmp = db::query("SELECT `url` FROM `texts` WHERE `url` LIKE ? ORDER BY `url` DESC", array($title.'%'))->fetch();
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
  
  public static function add()
  {
    load('views/texts/add');
  }
  
  public static function edit()
  {
    if (!empty(router::$segments[6]))
    {
      self::$vars['item'] = db::query("SELECT * FROM `texts` WHERE `id` = ? LIMIT 1", router::$segments[6])->fetch();
      load('views/texts/edit', self::$vars);
    }
  }
  
  
  public static function add_item()
  {
    if (fv::ispost('text', 'title', 'url'))
    {
      $nav = db::query("SELECT * FROM `navigation` WHERE `id` = ? LIMIT 1", router::segment(4))->fetch();
      if (!empty($nav->id))
      {
        $sort = db::query("SELECT max(`sort`) as sort FROM `texts` WHERE `nav_id` = ?", router::segment(4))->fetch();
        $sort = (empty($sort->sort) ? 1 : $sort->sort + 1);
  
        db::exec(
          "INSERT INTO `texts` SET `nav_id` = ?, `language` = ?, `title` = ?, `url` = ?, `text` = ?, `sort` = ?, `active` = ?",
          array(
            router::segment(4), 
            router::segment(2), 
            $_POST['title'], 
            $_POST['url'], 
            $_POST['text'], 
            $sort, 
            (empty($_POST['active']) ? 0 : 1)
          )
        );
        echo json_encode(array('redirect' => site_url('navigation/index/'. router::segment(2) .'/'. router::segment(3) .'/'. router::segment(4))));
      }
    }
  }

  public static function edit_item()
  {
    if (fv::ispost('item_id', 'text', 'title', 'url'))
    {
      db::exec(
        "UPDATE `texts` SET `title` = ?, `url` = ?, `text` = ?, `active` = ? WHERE `id` = ? LIMIT 1",
        array($_POST['title'], $_POST['url'], $_POST['text'], (empty($_POST['active']) ? 0 : 1), $_POST['item_id'])
      );

      echo json_encode(array('redirect' => site_url('navigation/index/'. router::segment(2) .'/'. router::segment(3) .'/'. router::segment(4))));
    }
  }
  
  public static function delete_item()
  {
    if (!empty(router::$segments[5]))
    {
      db::exec("DELETE FROM `texts` WHERE `id` = ? LIMIT 1", router::$segments[5]);
      router::redirect('navigation/index/'. router::segment(2) .'/'. router::segment(3) .'/'. router::segment(4));
    }
  }


  /* Navigation methods */
  public static function delete_nav($nav_id)
  {
    if (!empty($nav_id))
    {
      db::exec("DELETE FROM `texts` WHERE `nav_id` = ?", $nav_id);
    }
  }
  
  public static function nav_settings()
  {
    load('views/texts/nav_settings');
  }
}

?>