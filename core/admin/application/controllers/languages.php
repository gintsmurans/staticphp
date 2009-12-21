<?php


class languages
{
  private static $vars = array();



  public static function __construct__()
  {  
    user_model::check_access();
    
    load('models/languages_model');
    load_lang('admin_languages');

    // Language stuff
    self::$vars['languages'] = languages_model::get_columns();
    self::$vars['scopes'] = languages_model::get_scopes();

    if (!empty(router::$segments[2]))
    {
      $scope = db::query("SELECT `scope` FROM `languages` WHERE `scope` = ?", router::$segments[2])->fetch();
      if (!empty($scope))
      {
        self::$vars['current_scope'] = $scope->scope;
        self::$vars['translations'] = languages_model::get_languages(array('scope' => $scope->scope));
      }
    }

    //if (empty(self::$vars['translations']))
    //{
    // self::$vars['translations'] = languages_model::get_languages();
    //}
    
    
    css(base_url('css/languages.css'), base_url('css/jquery.wysiwyg.css'));
    js(base_url('js/jquery.wysiwyg.js'), base_url('js/languages.js'), base_url('languages/base_js'));
    js('inline:
      var languages = '. json_encode(languages_model::$fields) .';
      var current_scope = \''. (empty($scope->scope) ? '' : $scope->scope) .'\';
    ');
  }
  
  
  public static function base_js()
  {
    header('Content-Type: text/javascript');
    load('views/languages/base.js');
  }


  public static function index()
  {
    if (count(g('config')->languages) > count(languages_model::$fields))
    {
      self::$vars['error'] = str_replace('!url', site_url('languages/setup'), LANGUAGES_CONFIG_DIFFERENCE);
    }
    
    if (!empty($_SESSION['msg_failed']))
    {
      self::$vars['msg_failed'] = $_SESSION['msg_failed'];
      unset($_SESSION['msg_failed']);
    }
    
    if (!empty($_SESSION['msg_ok']))
    {
      self::$vars['msg_ok'] = $_SESSION['msg_ok'];
      unset($_SESSION['msg_ok']);
    }

    load('views/header');
    load('views/languages/index', self::$vars);
    load('views/footer');
  }
  
  
  public static function setup()
  {
    languages_model::setup();
    router::redirect(router::$class);
  }


  public static function add_language()
  {
    if (fv::ispost('lang'))
    {
      if (!in_array($_POST['lang'], self::$vars['languages']))
      {
        g('config')->languages[] = $_POST['lang'];
        languages_model::setup();
        self::$vars['languages'] = languages_model::get_columns();
      }
    }
  }
  
  
  public static function delete_language()
  {
    if (!empty(router::$segments[2]))
    {
      if (router::$segments[2] == g('config')->lang_default)
      {
        $_SESSION['msg_failed'] = LANGUAGES_ERROR1;
      }
      else if (in_array(router::$segments[2], g('config')->languages))
      {
        $_SESSION['msg_failed'] = LANGUAGES_ERROR2;
      }
      else
      {
        languages_model::drop_language(router::$segments[2]);
        $_SESSION['msg_ok'] = LANGUAGES_OK1;
      }
    }
    router::redirect(router::$class);
  }


  public static function activate_language()
  {
    if (!empty(router::$segments[2]))
    {
      if (!in_array(router::$segments[2], languages_model::$fields))
      {
        $_SESSION['msg_failed'] = LANGUAGES_ERROR3;
      }
      elseif (in_array(router::$segments[2], g('config')->languages))
      {
        $keys = array_flip(g('config')->languages);
        unset($keys[router::$segments[2]]);
        g('config')->languages = array_flip($keys);
        languages_model::write_languages();
        $_SESSION['msg_ok'] = str_replace('!lang', router::$segments[2], LANGUAGES_OK2);
      }
      else
      {
        g('config')->languages[] = router::$segments[2];
        languages_model::write_languages();
        $_SESSION['msg_ok'] = str_replace('!lang', router::$segments[2], LANGUAGES_OK3);
      }
    }
    router::redirect(router::$class);
  }
  
  
  public static function add_item()
  {
    if (fv::ispost(array('add_scope', 'add_ident')))
    {
      $_POST['add_ident'] = mb_strtoupper(str_replace('-', '_', fv::set_friendly($_POST['add_ident'])));
      if (empty($_POST['add_ident']))
      {
        $output = array('error' => LANGUAGES_ERROR4);
      }
      else
      {
        $result = languages_model::get_languages(array('ident' => $_POST['add_ident']));
        if (!empty($result))
        {
          $output = array('error' => LANGUAGES_ERROR5);
        }
        else
        {
		  $data['scope'] = $_POST['add_scope'];
          $data['ident'] = $_POST['add_ident'];
		  foreach (g('config')->languages as $lang)
		  {
			if (!empty($_POST['add_lang_'. $lang]))
			{
			  $data[$lang] = $_POST['add_lang_'. $lang];
			}
		  }
		  
          db::insert('languages', $data);
          $output = $data;
        }
      }

      // Output
      echo json_encode($output);
    }
  }


  public static function edit_item()
  {
    if (fv::ispost(array('ident', 'lang', 'value')))
    {
      if ($_POST['lang'] == 'ident')
      {
        $_POST['value'] = mb_strtoupper(str_replace('-', '_', fv::set_friendly($_POST['value'])));
      }
      elseif ($_POST['lang'] == 'scope')
      {
        $_POST['value'] = fv::set_friendly($_POST['value']);
      }

      languages_model::set(array($_POST['lang'] => rawurldecode($_POST['value'])), $_POST['ident']);
      echo json_encode(array('value' => $_POST['value']));
    }
  }


  public static function delete_item()
  {
    if (fv::ispost(array('ident')))
    {
      languages_model::delete($_POST['ident']);
      echo json_encode(array('ident' => $_POST['ident']));
    }
  }
  
  
  public static function copy_to_web()
  {
    $languages = (empty(router::$segments[2]) ? languages_model::$fields : (array) router::$segments[2]);
    languages_model::copy_to_web($languages);

    $_SESSION['msg_ok'] = LANGUAGES_OK4;

    router::redirect(router::$class);
  }
  
  
  public static function copy_from_web()
  {
    $languages = (empty(router::$segments[2]) ? languages_model::$fields : (array) router::$segments[2]);
    languages_model::copy_from_web($languages);
    $_SESSION['msg_ok'] = LANGUAGES_OK5;

    router::redirect(router::$class);
  }
  
  
  public static function copy_scope_to_web()
  {
    if (!empty(router::$segments[2]))
    {
      languages_model::copy_to_web(g('config')->languages, router::$segments[2]);
      $_SESSION['msg_ok'] = LANGUAGES_OK6;
    }

    router::redirect(router::$class .'/index/'. router::$segments[2]);
  }

  public static function copy_scope_from_web()
  {
    if (!empty(router::$segments[2]))
    {
      languages_model::copy_from_web(g('config')->languages, router::$segments[2]);
      $_SESSION['msg_ok'] = LANGUAGES_OK7;
    }

    router::redirect(router::$class .'/index/'. router::$segments[2]);
  }
}

?>