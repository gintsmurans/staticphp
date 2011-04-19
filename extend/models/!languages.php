<?php

class languages
{
  public static $country = NULL;
  public static $language = NULL;
  public static $lang_current = NULL;

  public static function init()
  {
    global $config;

    // Language support
    if ($config->lang_support === true)
    {
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


      if (!empty($redirect))
      {
        // Set country and language as prefixes
        //array_push(router::$prefixes, self::$lang_current['current']);
        //router::$prefixes_uri = implode('/', router::$prefixes);

        // Redirect to current language
        //echo site_url(router::$segments_uri);
        //echo site_url(router::$segments_uri);
        router::redirect(site_url(router::$segments_uri), false, true);
      }

      // Autoload language files from config
      foreach($config->lang_load as &$item)
      {
        self::load($item);
      }
    }
  }
  
  
  public static function load()
  {
    $dir = APP_PATH . 'languages/' . (empty(self::$lang_current['directory']) ? '' : self::$lang_current['directory'] . '/') . self::$lang_current['current'] .'/';
    foreach (func_get_args() as $file)
    {
      include $dir . $file . '_lang.php';
    }
  }
}

?>