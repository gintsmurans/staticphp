<?php


class languages
{
  private static $vars = array();



  public static function __construct__()
  {
    load('models/languages_model');

    user_model::check_access();
    
    // Language stuff
    self::$vars['languages'] = languages_model::get_columns();
    self::$vars['scopes'] = languages_model::get_scopes();

    $data = (!empty(router::$segments[2]) && !empty(self::$vars['scopes']) && in_array(router::$segments[2], self::$vars['scopes']) ? array('scope' => router::$segments[2]) : null);
    self::$vars['translations'] = languages_model::get_languages($data);
  }


  public static function index()
  {
    if (count(g('config')->languages) > count(languages_model::$fields))
    {
      self::$vars['error'] = 'There is difference between table columns and configuration file language array <a href="'.site_url('languages/setup').'>Change language database!</a>';
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
    load('views/languages', self::$vars);
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
        $_SESSION['msg_failed'] = 'Failed! You can\'t delete the default language.';
      }
      else if (in_array(router::$segments[2], g('config')->languages))
      {
        $_SESSION['msg_failed'] = 'Failed! Set language to inactive and then delete it.';
      }
      else
      {
        languages_model::drop_language(router::$segments[2]);
        $_SESSION['msg_ok'] = 'Ok! Language deleted.';
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
        $_SESSION['msg_failed'] = 'Failed! Can\'t find language in database.';
      }
      elseif (in_array(router::$segments[2], g('config')->languages))
      {
        $keys = array_flip(g('config')->languages);
        unset($keys[router::$segments[2]]);
        g('config')->languages = array_flip($keys);
        languages_model::write_languages();
        $_SESSION['msg_ok'] = 'Ok! Disabled language "'. router::$segments[2] .'".';
      }
      else
      {
        g('config')->languages[] = router::$segments[2];
        languages_model::write_languages();
        $_SESSION['msg_ok'] = 'Ok! Enabled language "'. router::$segments[2] .'".';        
      }
    }
    router::redirect(router::$class);
  }
  
  
  public static function add_item()
  {
    if (fv::ispost('ident'))
    {
      $_POST['ident'] = mb_strtoupper(str_replace('-', '_', fv::set_friendly($_POST['ident'])));
      if (empty($_POST['ident']))
      {
        $output = array('error' => 'Failed! There was no correct "ident" provided!');
      }
      else
      {
        $result = languages_model::get_languages(array('ident' => $_POST['ident']));
        if (!empty($result))
        {
          $output = array('error' => 'Failed! Ident with the same name already exists.');
        }
        else
        {
          db::exec("INSERT INTO `languages` SET `ident` = ?", $_POST['ident']);
          $output = array('ident' => $_POST['ident']);
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
    if (empty(router::$segments[2]))
    {
      $languages = languages_model::$fields;
    }
    else
    {
      if (!in_array(router::$segments[2], g('config')->languages))
      {
        $_SESSION['msg_failed'] = 'Failed! Language needs to be enabled.';
      }
      else
      {
        $languages = (array) router::$segments[2];
      }
    }

    if (!empty($languages))
    {
      languages_model::copy_to_web($languages);      
      $_SESSION['msg_ok'] = 'Ok! Language(-s) is copied to the server.';
    }

    router::redirect(router::$class);
  }
  
  
  public static function copy_from_web()
  {
    if (!empty(router::$segments[2]))
    {
      if (!in_array(router::$segments[2], g('config')->languages) || !is_dir(g('config')->lang_path . router::$segments[2] .'/'))
      {
        $_SESSION['msg_failed'] = 'Failed! Language needs to be enabled and copied to website.';
      }
      else
      {
        languages_model::copy_from_web(router::$segments[2]);
        $_SESSION['msg_ok'] = 'Ok! Language(-s) is copied to the server.';
      }
    }

    router::redirect(router::$class);
  }
}

?>