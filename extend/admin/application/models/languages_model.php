<?php


class languages_model
{

    public static $fields = array();


    public static function get_languages($add = array())
    {
        $where = '';
        $data = null;

        if (!empty($add) && is_array($add))
        {
            $where = " WHERE ".implode(' = ?, ', array_keys($add)).' = ?';
            $data = array_values($add);
        }

        return db::query("SELECT * FROM `languages`{$where}", $data)->fetchAll();
    }
    
    
    public static function get_scopes()
    {
        return db::query("SELECT `scope` FROM `languages` GROUP BY `scope`")->fetchAll();
    }
    
    
    public static function get_columns()
    {
      $result = db::query("SHOW FULL COLUMNS FROM `languages`")->fetchAll();
      foreach ($result as $tmp)
      {
        if ($tmp->Field == $tmp->Comment)
        {
          self::$fields[] = $tmp->Field;
        }
      }
      return self::$fields;
    }


    public static function setup()
    {
      foreach (g('config')->languages as $lang)
      {
        if (!in_array($lang, self::$fields))
        {
          db::exec("ALTER TABLE `languages` ADD COLUMN `{$lang}` longtext COMMENT '{$lang}';");
        }
      }
    }
    
    
    public static function drop_language($lang)
    {
      if (in_array($lang, self::$fields))
      {
        db::exec("ALTER TABLE `languages` DROP COLUMN `{$lang}`;");
        
        if (is_dir(g('config')->lang_path . $lang .'/'))
        {
          $e = opendir(g('config')->lang_path . $lang .'/');
          while ($item = readdir($e))
          {
            if (!is_dir(g('config')->lang_path . $lang .'/'. $item))
            {
              unlink(g('config')->lang_path . $lang .'/'. $item);
            }
          }
          rmdir(g('config')->lang_path . $lang .'/');
        }
      }
    }
    
    
    public static function write_languages()
    {
      $languages = "'". implode("', '", g('config')->languages) ."'";
      $html = load('views/languages/config_sample', array('%date' => date('Y-m-d H:i:s'), '%languages' => $languages), null, true);
      file_put_contents(g('config')->lang_path  .'/config.php', $html);
    }
    
    
    public static function copy_to_web($languages)
    {
      $result = languages_model::get_languages();
      foreach ($result as $item)
      {
        foreach ($languages as $lang)
        {
          if (!empty($item->scope) && !empty($item->ident) && in_array($lang, g('config')->languages))
          {
            if (empty($items[$lang][$item->scope]))
            {
              $items[$lang][$item->scope] = '';
            }
            
            $items[$lang][$item->scope] .= "define('". $item->ident ."', '". str_replace("'", "\'", $item->{$lang}) ."');\n";
          }
        }
      }
      
      foreach ($items as $lang => $item)
      {
        foreach ($item as $scope => $data)
        {
          $html = load('views/languages/language_sample', array('%lang' => strtoupper($lang), '%date' => date('Y-m-d H:i:s'), '%translations' => $data), null, true);
          if (!is_dir(g('config')->lang_path . $lang .'/'))
          {
            mkdir(g('config')->lang_path . $lang .'/');
          }
          file_put_contents(g('config')->lang_path . $lang .'/'. $scope .'_lang.php', $html);
        }
      }
    }


    public static function copy_from_web($lang)
    {
      if (is_dir(g('config')->lang_path . $lang .'/'))
      {
        $dh = opendir(g('config')->lang_path . $lang .'/');
        while ($file = readdir($dh))
        {
          if ($file[0] == '.' || !preg_match('/_lang.php/', $file))
          {
            continue;
          }

          $scope = str_replace('_lang.php', '', $file);
          $defined = get_defined_constants(true);
          $defined = $defined['user'];
          
          include g('config')->lang_path . $lang .'/'. $file;

          $defined2 = get_defined_constants(true);
          $defined2 = $defined2['user'];
          
          $difference = array_diff_key($defined2, $defined);
          
          foreach ($difference as $ident => $value)
          {
            $result = self::get_languages(array('ident' => $ident));
            if (!empty($result))
            {
              db::query("UPDATE `languages` SET `{$lang}` = ? WHERE `ident` = ? LIMIT 1", array($value, $ident));
            }
            else
            {
              db::query("INSERT INTO `languages` SET `scope` = ?, `{$lang}` = ?, `ident` = ?", array($scope, $value, $ident));
            }
          }
        }
      }
    }


    public static function set($data, $id)
    {
        // Prepare query
        if ($id == 'new')
        {
            $names = implode(",", array_keys($data));
            $values = implode(",", array_fill(0, count($data), '?'));

            db::exec("INSERT INTO `languages` ($names) VALUES ($values)", array_values($data));
        }
        else
        {
            $set = implode(' = ?', array_keys($data)).' = ?';
            $data[] = $id;

            db::exec("UPDATE `languages` SET $set WHERE `ident` = ? LIMIT 1", array_values($data));
        }
    }
    
    
    
    public static function delete($id)
    {
        if (!empty($id))
        {
            db::exec("DELETE FROM `languages` WHERE `ident` = ? LIMIT 1", array($id));
        }
    }
}


?>