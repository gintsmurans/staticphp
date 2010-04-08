<?php

class i18n
{
  public static $country = NULL;
  public static $language = NULL;
  public static $lang_current = NULL;

  public static function init()
  {
    global $config;

    // Split router segments
    router::split_segments();


    // ============================== COUNTRY ===============================

    self::$lang_current = &reset($config->lang_available);

    // Search for current country in URI
    if (isset(router::$segments[0]) && isset($config->lang_available[router::$segments[0]]))
    {
      self::$lang_current = &$config->lang_available[router::$segments[0]];
      self::$country = router::$segments[0];

      array_push(router::$prefixes, router::$segments[0]);
      array_shift(router::$segments);
    }

    // Search for current country in lang_key
    if (!empty($config->lang_key))
    {
      foreach ($config->lang_available as $key => &$item)
      {
        if (preg_match('/'. $key .'/', $config->lang_key))
        {
          self::$lang_current = &$item;
          self::$country = $key;

          array_push(router::$prefixes, $key);
          array_shift(router::$segments);
          break;
        }
      }
    }

    // Redirect country
    if (empty(self::$country) && !empty($config->lang_country_redirect))
    {
      array_push(router::$prefixes, key($config->lang_available));
      $redirect = true;
    }


    // ============================== LANGUAGES ===============================

    self::$lang_current['current'] = self::$lang_current['languages'][0];

    // Search for current language
    if (!empty(router::$segments[0]) && in_array(router::$segments[0], self::$lang_current['languages']))
    {
      self::$lang_current['current'] = router::$segments[0];
      array_shift(router::$segments);
    }
    elseif (!empty($config->lang_redirect))
    {
      $redirect = true;
    }


    // ============================== FINAL ==============================

    self::$language = &self::$lang_current['current'];

    // Set country and language as prefixes
    array_push(router::$prefixes, self::$lang_current['current']);
    router::$prefixes_uri = implode('/', router::$prefixes);
    router::$segments_uri = implode('/', router::$segments);


    // Redirect to current language
    if (!empty($redirect))
    {
      router::redirect(site_url(router::$segments_uri), false, true);
    }

    // Autoload language files from config
    foreach($config->lang_load as &$item)
    {
      self::load($item);
    }
  }


  public static function load()
  {
    $dir1 = APP_PATH . 'cache/';
    foreach (func_get_args() as $scope)
    {
      // Make hashes and paths
      $hash = sha1(self::$country . self::$language . $scope);
      $dir2 = $dir1 . substr($hash, -2, 2) . '/' . substr($hash, -4, 2). '/';
      $file = $dir2 . $hash;

      // Check if needs to update the cache
      $res1 = ini_get('apc.enabled') ? apc_fetch($hash) : db::fetch('SELECT * FROM cache WHERE id = ?', $hash);

      if (empty($res1))
      {
        $contents = "<?php\n\n";

        $res2 = db::fetchAll('
          SELECT t1.id, t1.ident, t1.default, t2.'. self::$language .' as lang
          FROM i18n as t1
          LEFT JOIN '. self::$lang_current['table'] .' as t2
            ON t2.id = t1.id
          WHERE t1.scope = ?
          ORDER BY ident
        ', array($scope));

        foreach ($res2 as $item)
        {
          if (empty($item->lang))
          {
            $item->lang = $item->default;
          }
          $item->lang = str_replace("'", "\\'", $item->lang);
          $contents .= "define('{$item->ident}', '{$item->lang}');\n";
        }

        // Create directories
        if (!is_dir($dir2))
        {
          mkdir($dir2, 0777, true);
        }

        // Put contents to the file
        file_put_contents($file, $contents);

        // Save cached status
        ini_get('apc.enabled') ? apc_store($hash, 1) : db::query('INSERT INTO cache (id, value) VALUES (?, 1)', $hash);
      }

      // Load file from the cache and return result from database
      include $file;
    }
  }


  public static function item($ident, $replace = array())
  {
    return empty($replace) ? constant($ident) : str_replace(array_keys($replace), $replace, constant($ident));
  }
}

?>