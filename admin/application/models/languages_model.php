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

        return db::query("SELECT * FROM `languages`{$where} ORDER BY `scope`, `ident`", $data)->fetchAll();
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
      $html = strtr(file_get_contents(APP_PATH .'views/languages/config_sample.php'), array('%date' => date('Y-m-d H:i:s'), '%languages' => $languages));
      file_put_contents(g('config')->lang_path  .'/config.php', $html);
    }
    
    
    public static function copy_to_web($languages, $scope = null)
    {
      $result = languages_model::get_languages((empty($scope) ? '' : array('scope' => $scope)));
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
          $html = strtr(file_get_contents(APP_PATH .'views/languages/language_sample.php'), array('%lang' => strtoupper($lang), '%date' => date('Y-m-d H:i:s'), '%translations' => $data));
          if (!is_dir(g('config')->lang_path . $lang .'/'))
          {
            mkdir(g('config')->lang_path . $lang .'/');
          }
          file_put_contents(g('config')->lang_path . $lang .'/'. $scope .'_lang.php', $html);
        }
      }
    }


    public static function copy_from_web($languages, $scope = null)
    {
      foreach ($languages as $lang)
      {
        if (is_dir(g('config')->lang_path . $lang .'/'))
        {
          $dh = opendir(g('config')->lang_path . $lang .'/');
          while ($file = readdir($dh))
          {
            // Check for the real files
            if ($file[0] == '.' || !preg_match('/_lang.php/', $file))
            {
              continue;
            }

            // Get / Check scopes
            $file_scope = str_replace('_lang.php', '', $file);
            if (!empty($scope) && $scope != $file_scope)
            {
              continue;
            }

            // Get all defined from the file
            $test = file_get_contents(g('config')->lang_path . $lang .'/'. $file);
            $matches = '';

            preg_match_all('/define ?\( ?[\'"](.*)\', \'(.*)[\'"] ?\)/', $test, $matches);            
            if (empty($matches[1]))
            {
              continue;
            }

            // Insert them into db
            foreach ($matches[1] as $key => $ident)
            {
              $matches[2][$key] = str_replace("\\'", "'", $matches[2][$key]);
              $result = self::get_languages(array('ident' => $ident));
              if (!empty($result))
              {
                db::query("UPDATE `languages` SET `{$lang}` = ? WHERE `ident` = ? LIMIT 1", array($matches[2][$key], $ident));
              }
              else
              {
                db::query("INSERT INTO `languages` SET `scope` = ?, `{$lang}` = ?, `ident` = ?", array($file_scope, $matches[2][$key], $ident));
              }
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