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


    public static function set($data, $id)
    {
        // Prepare query
        if ($id == 'new')
        {
            $names = implode(",", array_keys($data));
            $values = implode(",", array_fill(0, count($data), '?'));

            languages::exec("INSERT INTO `languages` ($names) VALUES ($values)", array_values($data));

            return Languages::db_link()->lastInsertId();
        }
        else
        {
            $set = implode(' = ?', array_keys($data)).' = ?';
            $data[] = $id;

            languages::exec("UPDATE `languages` SET $set WHERE `id` = ?", array_values($data));
        }
    }
    
    
    
    public static function delete($id)
    {
        if (!empty($id))
        {
            languages::exec("DELETE FROM `languages` WHERE `id` = ?", array($id));
        }
    }
}


?>