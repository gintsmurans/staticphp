<?php


class language
{


    private static $vars = array();



    public static function construct()
    {
        load('helpers/forms');

        load(ADMIN_PATH.'models/mlanguage_admin');

        if (empty($_SESSION['language_auth']))
        {
            language::auth();
        }
        
        $data = (!empty(router::$segments[2]) ? array('scope' => router::$segments[2]) : null);
        self::$vars['translations'] = mlanguage_admin::get_languages($data);
        self::$vars['tr_keys'] = mlanguage_admin::get_columns();
        self::$vars['scopes'] = mlanguage_admin::get_scopes();        
    }
    
    
    public static function auth()
    {
        
        if (ispost())
        {
            if (g('config')->admin_password == sha1($_POST['p']))
            {
                $_SESSION['language_auth'] = true;
                $output = array('done' => true);
            }
            else
            {
                $output = array('error' => 'Wrong!');
            }
            
            echo json_encode($output);
        }
        else
        {
            load('views/password');
        }
        
        exit;
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
        if (ispost(array('id', 'lang', 'value')))
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
        if (ispost(array('id')))
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