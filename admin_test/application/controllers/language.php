<?php


class language
{


  private static $vars = array();



  public static function __construct__()
  {
    load('models/mlanguage_admin');

    user_model::check_access();
    
    $data = (!empty(router::$segments[2]) ? array('scope' => router::$segments[2]) : null);
    self::$vars['translations'] = mlanguage_admin::get_languages($data);
    self::$vars['tr_keys'] = mlanguage_admin::get_columns();
    self::$vars['scopes'] = mlanguage_admin::get_scopes();        
  }


  public static function index()
  {
    if (isset(self::$vars['tr_keys']) && count(g('config')->languages) != count(self::$vars['tr_keys']))
    {
      self::$vars['error'] = 'There is difference between table columns and configuration file language array <a href="'.site_url('admin/language/setup').'>Change language database!</a>';
    }

    load('views/language', self::$vars);
  }
  
  
  public static function setup()
  {
    $prefixes = g('config')->languages;
    
    if (empty(mlanguage_admin::$fields))
    {
       // Languages::db_link()->exec("CREATE TABLE languages(id INTEGER PRIMARY KEY AUTOINCREMENT, ".(implode(' TEXT, ', $prefixes).' TEXT').");");
       // Languages::db_link()->exec("CREATE INDEX ON `languages`(ident);");
    }
    else
    {
      foreach ($prefixes as $index=>$lang)
      {
        if (!in_array($index, self::$vars['tr_keys']))
        {
          Languages::db_link()->exec("ALTER TABLE `languages` ADD COLUMN ".$index." text;");
        }
      }
    }

    router::redirect('language');
  }
  
  
  public static function set()
  {
    if (fv::ispost(array('id', 'lang', 'value')))
    {
      $new_id = mlanguage_admin::set(array($_POST['lang'] => rawurldecode($_POST['value'])), $_POST['id']);
      echo json_encode(array('id' => $new_id));
    }
    else
    {
      router::redirect('language');
    }
  }


  public static function delete()
  {
    if (fv::ispost(array('id')))
    {
      mlanguage_admin::delete($_POST['id']);
      echo json_encode(array('id' => $_POST['id']));
    }
    else
    {
      router::redirect('language');
    }
  }
}

?>