<?php

/*
	Internationalization class
	--
	i18n::$country - current country
	i18n::$countries - all available countries
	i18n::$currnet - array holding current country settings
	i18n::$language - current language
*/

class i18n
{
	public static $country = NULL;
	public static $countries = NULL;
	public static $current = NULL;
  public static $language = NULL;

	private static $l = array();


	public static function make_hash()
	{
		return sha1(self::$country . self::$language);
	}

  public static function init()
  {
    global $config;

    // Split router segments
    router::split_segments();


    // COUNTRY
		self::$countries = $config->lang_available;
    self::$current = &reset(self::$countries);

    // Search for current country in URI
    if (isset(router::$segments[0]) && isset(self::$countries[router::$segments[0]]))
    {
      self::$current = &self::$countries[router::$segments[0]];
      self::$country = router::$segments[0];

      array_push(router::$prefixes, router::$segments[0]);
      array_shift(router::$segments);
    }

    // Search for current country in lang_key
    if (!empty($config->lang_key))
    {
      foreach (self::$countries as $key => &$item)
      {
        if (preg_match('/'. $key .'/', $config->lang_key))
        {
          self::$current = &$item;
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
      array_push(router::$prefixes, key(self::$countries));
      $redirect = TRUE;
    }


    // LANGUAGES
    self::$language = self::$current['languages'][0];

    // Search for current language
    if (!empty(router::$segments[0]) && in_array(router::$segments[0], self::$current['languages']))
    {
      self::$language = router::$segments[0];
      array_shift(router::$segments);
    }
    elseif (!empty($config->lang_redirect))
    {
      $redirect = TRUE;
    }


    // FINAL
    // Set country and language as prefixes
    array_push(router::$prefixes, self::$language);
    router::$prefixes_uri = implode('/', router::$prefixes);
    router::$segments_uri = implode('/', router::$segments);

    // Redirect to current language
    if (!empty($redirect))
    {
      router::redirect(site_url(router::$segments_uri), FALSE, TRUE);
    }

		// Load languages
		self::load();
  }


  public static function load()
  {
		// Cache directory
    $dir1 = APP_PATH . 'cache/';

		// Make hashes and paths
		$hash = self::make_hash();
		$dir2 = $dir1 . substr($hash, -2, 2) . '/' . substr($hash, -4, 2). '/';
		$file = $dir2 . $hash . '.php';

		// Check if needs to update the cache
		$res1 = ini_get('apc.enabled') ? apc_fetch($hash) : db::fetch('SELECT * FROM cache WHERE id = ? AND server_id = ?', array($hash, g('config')->server_id));
		if (!empty(g('config')->debug) || empty($res1))
		{
			try
			{
				$res2 = db::fetchAll('SELECT id, '. self::$current['code'] .'_'. self::$language .' as lang FROM i18n ORDER BY id');
			}
			catch(Exception $e)
			{
				$res2 = db::fetchAll('SELECT id, "" as lang FROM i18n ORDER BY id');
			}

			if (!empty($res2))
			{
				$contents = "<?php\n\n# Country: ". self::$country ."\n# Language: ". self::$language ."\n\n";

				// Walk through the result
				foreach ($res2 as $item)
				{
					$item->id = str_replace("'", "\\'", $item->id);
					$item->lang = str_replace("'", "\\'", $item->lang);
					$contents .= "\$l['{$item->id}'] = '{$item->lang}';\n";
				}

				// Create directories
				if (!is_dir($dir2))
				{
					mkdir($dir2, 0777, TRUE);
				}

				// Put contents to the file
				file_put_contents($file, $contents);

				// Save cached status
				if (empty(g('config')->debug))
				{
					ini_get('apc.enabled') ? apc_store($hash, 1) : db::query('INSERT INTO cache (id, server_id, type, value) VALUES (?, ?, ?, 1)', array($hash, g('config')->server_id, 'i18n'));
				}
			}
		}

		// Load file from the cache
		if (is_file($file))
		{
			include $file;
			if (isset($l))
			{
				self::$l = array_merge(self::$l, $l);
				unset($l);
			}
		}
  }


  public static function item($ident, $replace = array(), $escape = NULL)
  {
    return empty($replace) ? constant($ident) : str_replace(array_keys($replace), $replace, constant($ident));
  }
	
	public static function _($text, $replace = array(), $escape = NULL)
	{
		// If debug is enabled do some dirty stuff
		if (!empty($config->debug) && isset(self::$l[$text]))
		{
			db::query('UPDATE i18n SET last_access = NOW() WHERE id = ?', array($text));
		}
		elseif (!isset(self::$l[$text]))
		{
			db::query('INSERT INTO i18n (id) VALUES (?)', $text);
			self::$l[$text] = $text;

			// Clear cache
			$hash = self::make_hash();
			ini_get('apc.enabled') ? apc_delete($hash) : db::query("DELETE FROM cache WHERE type = 'i18n'");
		}

		// Set text to translation if is not empty
		if (!empty(self::$l[$text]))
		{
			$text = self::$l[$text];
		}

		// Do some output escaping, if pointed
		switch ($escape)
		{
			case 'js':
				$text = str_replace(array("'", "\r", "\n"), array("\\'", '', ''), $text);
			break;
			
			case 'input':
				$text = str_replace('"', '&quot;', $text);
			break;
		}

		// Return text, replace if necessary
		return empty($replace) ? $text : str_replace(array_keys($replace), $replace, $text);
	}
}

?>