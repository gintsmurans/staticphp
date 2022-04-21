<?php

// TODO: Needs revision, specificly Cache part

namespace System\Modules\Utils\Models;

use System\Modules\Core\Models\Config;
use System\Modules\Core\Models\Router;
use System\Modules\Utils\Models\Db;
use System\Modules\Utils\Models\Cache;

/**
 *  Internationalization (i18n).
 */
class i18n
{
    /**
     *  Array holding all i18n config.
     *
     * (default value: null)
     *
     * @var array
     * @access public
     * @static
     */
    public static $config = null;

    /**
     *  Array holding info of all available countries.
     *
     * (default value: null)
     *
     * @var array
     * @access public
     * @static
     */
    public static $countries = null;

    /**
     *  Currently active country.
     *
     * (default value: null)
     *
     * @var array
     * @access public
     * @static
     */
    public static $current_country = null;

    /**
     *  Current country's abbreviation code.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $country_code = null;

    /**
     *  Current language's abbreviation code.
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $language_code = null;

    /**
     *  Current url prefix
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $url_prefix = null;

    /**
     *  Language key to look for in database
     *
     * (default value: null)
     *
     * @var string
     * @access public
     * @static
     */
    public static $language_key = null;

    /**
     *  Cache key prefix for setting and getting cached values
     *
     * (default value: '')
     *
     * @var string
     * @access public
     * @static
     */
    private static $cache_key_prefix = '';

    /**
     *  Current country's and language's cached strings.
     *
     * (default value: [])
     *
     * @var array
     * @access public
     * @static
     */
    private static $cache = [];


    /**
     *  Debug: whether to cache or not to cache language translation strings
     *
     * (default value: false)
     *
     * @var bool
     * @access public
     * @static
     */
    private static $debug = false;

    /*
     * =============================================== Main Methods ====================================================
     */

    /**
     *  Country & language hash value.
     *
     * @access public
     * @static
     * @return string
     */
    public static function hash()
    {
        return sha1(self::$country_code . self::$language_code);
    }

    /**
     *  Make country and language prefix.
     *
     * @access public
     * @static
     * @param  array $country
     * @param  string $language
     * @return string
     */
    public static function urlPrefix($country, $language)
    {
        return str_replace(['{{country}}', '{{language}}'], [$country['code'], $language], self::$config['url_format']);
    }

    /**
     * Prints debug information.
     *
     * @access public
     * @static
     * @return void
     */
    public static function debug()
    {
        echo "i18n::\$url_prefix";
        print_r(self::$url_prefix);
        echo "\n";

        echo "i18n::\$country_code: ";
        print_r(i18n::$country_code);
        echo "\n";

        echo "i18n::\$language_code: ";
        print_r(i18n::$language_code);
        echo "\n";

        echo "i18n::\$current_country: ";
        print_r(i18n::$current_country);
        echo "\n";

        echo "i18n::\$countries: ";
        print_r(i18n::$countries);
        echo "\n";

        echo "i18n::\$config: ";
        print_r(i18n::$config);
        echo "\n";

        echo "i18n::\$cache: ";
        print_r(i18n::$cache);
        echo "\n";
    }

    /**
     *  Init stuff.
     *
     * @access public
     * @static
     * @return void
     */
    public static function init($country = null, $language = null)
    {
        // If i18n config is not already loaded, do it now
        if (empty(Config::$items['i18n'])) {
            Config::load(['i18n'], null, 'System');
        }

        self::$debug = Config::$items['debug'];

        // Default country
        self::$config = &Config::$items['i18n'];
        self::$countries = &self::$config['available'];
        self::$current_country = reset(self::$countries);
        self::$country_code = &self::$current_country['code'];
        self::$language_code = reset(self::$current_country['languages']);

        $found_country_language = false;
        if ($country !== null && $language !== null) {
            foreach (self::$countries as &$country_item) {
                if ($country_item['code'] == $country) {
                    if (in_array($country, $country_item['languages'])) {
                        self::$current_country = &$country_item;
                        self::$country_code = &self::$current_country['code'];
                        self::$language_code = $language;
                        self::$url_prefix = self::urlPrefix($country_item, $language);

                        $found_country_language = true;
                        break;
                    }
                }
            }
        } else {
            // Search for current country in URI
            foreach (self::$countries as &$country_item) {
                foreach ($country_item['languages'] as &$language_item) {
                    $test = self::urlPrefix($country_item, $language_item);
                    if (in_array($test, Router::$prefixes)) {
                        self::$current_country = &$country_item;
                        self::$country_code = &self::$current_country['code'];
                        self::$language_code = &$language_item;
                        self::$url_prefix = $test;

                        $found_country_language = true;
                        break;
                    }
                }
            }
        }

        // Redirect to default language
        if ($found_country_language === false) {
            if (!empty(self::$config['redirect'])) {
                $url  = self::urlPrefix(self::$current_country, self::$language_code);
                $url .= Router::$requested_url;
                Router::redirect($url);
            } else {
                self::$url_prefix = self::urlPrefix(self::$current_country, self::$language_code);
            }
        }

        // Key
        self::$language_key = self::$country_code . '_' . self::$language_code;
        self::$cache_key_prefix = self::$config['cache_prefix'];

        // Load cache, if external
        if (self::$config['cache'] === 'external') {
            Cache::init();
        }

        // Load languages
        self::load();
    }

    /**
     *  Load strings.
     *
     * @access public
     * @static
     * @return void
     */
    public static function load($language_key = null)
    {
        if ($language_key === null) {
            $language_key = self::$language_key;
        }

        $db_scheme = (Config::$items['i18n']['db_scheme'] ? Config::$items['i18n']['db_scheme'] . '.' : '');
        $cached = null;
        if (self::$debug !== true) {
            $cached = Db::fetch(
                "
                    SELECT created FROM {$db_scheme}i18n_cached WHERE id = ? LIMIT 1
                ",
                [$language_key],
                self::$config['db_config']
            );
        }
        if (empty($cached)) {
            $res = Db::fetchAll(
                "
                    SELECT keys.key, tr.value FROM {$db_scheme}i18n_keys AS keys
                    LEFT JOIN {$db_scheme}i18n_translations AS tr ON tr.key_id = keys.id AND tr.language = ?
                    ORDER BY keys.id
                ",
                [$language_key],
                self::$config['db_config']
            );

            self::$cache[$language_key] = [];
            foreach ($res as $item) {
                self::$cache[$language_key][$item['key']] = $item['value'];
            }

            self::cacheWrite($language_key, $res);
            if (self::$debug !== true) {
                self::cacheApprove($language_key);
            }
        } else {
            $items = self::cacheRead($language_key);
            if (is_array($items) != true) {
                self::cacheInvalidate($language_key);
                self::load();
                return;
            }

            self::$cache[$language_key] = &$items;
        }
    }

    /**
     *  Returns all cached string.
     *
     * @access public
     * @static
     * @return void
     */
    public static function cache($language_key = null)
    {
        if ($language_key === null) {
            $language_key = self::$language_key;
        }

        return self::$cache[$language_key] ?? [];
    }

    /**
     *  Not sure.
     *
     * @access public
     * @static
     * @param  string $ident
     * @param  array $replace
     * @param  null $escape
     * @return string
     */
    public static function item($ident, $replace = [], $escape = null)
    {
        return empty($replace) ? constant($ident) : str_replace(array_keys($replace), $replace, constant($ident));
    }

    /**
     * Gets translated text.
     *
     * @access public
     * @static
     * @param  string $text Text to translate
     * @param  array $replace Replace stuff
     * @param  null $escape Escape some types of chars, for example for javascript or html input parameters
     * @return string
     */
    public static function translate($text, $replace = [], $escape = null, $language_key = null)
    {
        if (empty(self::$config)) {
            throw new \Exception('Init hasn\'t been called yet');
        }

        if ($language_key === null) {
            $language_key = self::$language_key;
        } elseif (!isset(self::$cache[$language_key])) {
            self::load($language_key);
        }

        if (empty(self::$cache[$language_key][$text])) { // A note: using isset returns false when value NULL is returned from postgresql
            $db_scheme = (Config::$items['i18n']['db_scheme'] ? Config::$items['i18n']['db_scheme'] . '.' : '');
            $record = Db::fetch("SELECT id FROM {$db_scheme}i18n_keys WHERE key = ?", [$text], self::$config['db_config']);
            if (empty($record)) {
                $record = Db::fetch("INSERT INTO {$db_scheme}i18n_keys (key) VALUES (?) RETURNING id", [$text], self::$config['db_config']);
            }

            self::$cache[$language_key][$text] = $text . '*';
            Db::query(
                "INSERT INTO {$db_scheme}i18n_translations (key_id, language, value) VALUES (?, ?, ?)",
                [$record['id'], $language_key, $text . '*'],
                self::$config['db_config']
            );

            // Clear cache
            self::cacheInvalidate($language_key);
        }

        // Set text to translation if its not empty
        if (!empty(self::$cache[$language_key][$text])) {
            $text = self::$cache[$language_key][$text];
        }

        // Do some output escaping, if pointed
        switch ($escape) {
            case 'js':
                $text = str_replace(["'", "\r", "\n"], ["\\'", '', ''], $text);
                break;

            case 'input':
                $text = str_replace('"', '&quot;', $text);
                break;
        }

        // Return text, replace if necessary
        return empty($replace) ? $text : str_replace(array_keys($replace), $replace, $text);
    }


    /**
     * Update translated $text by $key.
     *
     * @access public
     * @static
     * @param  string $key Key to update
     * @param  array $text Text to update
     * @param  null $language_key Language key for which to update
     * @return string
     */
    public static function update($key, $text, $language_key = null)
    {
        if (empty(self::$config)) {
            throw new \Exception('Init hasn\'t been called yet');
        }

        if ($language_key === null) {
            $language_key = self::$language_key;
        } elseif (!isset(self::$cache[$language_key])) {
            self::load($language_key);
        }

        if (!isset(self::$cache[$language_key][$key])) {
            throw new \Exception("Key \"{$key}\" doesn't exist");
        }

        $db_scheme = (Config::$items['i18n']['db_scheme'] ? Config::$items['i18n']['db_scheme'] . '.' : '');
        $record = Db::fetch("SELECT id FROM {$db_scheme}i18n_keys WHERE key = ?", [$key], self::$config['db_config']);
        if (empty($record)) {
            throw new \Exception("Key \"{$key}\" doesn't exist #2");
        }

        Db::query(
            "UPDATE {$db_scheme}i18n_translations SET value = ? WHERE key_id = ?",
            [$text, $record['id']],
            self::$config['db_config']
        );

        // Clear cache
        self::cacheInvalidate($language_key);
    }


    /*
     * =============================================== Twig ============================================================
     */

    /**
     * Register twig methods
     *
     * @access public
     * @static
     * @return void
     */
    public static function twigRegister()
    {
        // Variables
        Config::$items['view_data']['i18n']['country_code'] = &self::$country_code;
        Config::$items['view_data']['i18n']['language_code'] = &self::$language_code;
        Config::$items['view_data']['i18n']['url_prefix'] = &self::$url_prefix;
        Config::$items['view_data']['i18n']['countries'] = &self::$countries;

        // Register filters
        $filter = new \Twig\TwigFilter(
            'translate',
            function ($text, $replace = [], $escape = null, $language_key = null) {
                return \System\Modules\Utils\Models\i18n::translate($text, $replace, $escape, $language_key);
            },
            ['is_safe' => ['html']]
        );
        Config::$items['view_engine']->addFilter($filter);


        // Register functions
        $filter = new \Twig\TwigFunction('_', function ($text, $replace = [], $escape = null, $language_key = null) {
            return \System\Modules\Utils\Models\i18n::translate($text, $replace, $escape, $language_key);
        }, ['is_safe' => ['html']]);
        Config::$items['view_engine']->addFunction($filter);
    }


    /*
     * =============================================== Cache ===========================================================
     */

    /**
     * Returns path to a cache file
     *
     * @access public
     * @static
     * @return string
     */
    public static function cacheFile($language_key)
    {
        $cache_dir = APP_PATH . '/Cache/' . self::$config['cache_subdir'];
        $cache_file = $cache_dir . '/' . self::$cache_key_prefix . $language_key . '.php';

        // Create directories
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        return $cache_file;
    }

    /**
     * Invalidates the cache
     *
     * @access public
     * @static
     * @return void
     */
    public static function cacheInvalidate($language_key)
    {
        $db_scheme = (Config::$items['i18n']['db_scheme'] ? Config::$items['i18n']['db_scheme'] . '.' : '');
        Db::query(
            "DELETE FROM {$db_scheme}i18n_cached WHERE id = ?;",
            [$language_key],
            self::$config['db_config']
        );
    }

    /**
     * Approves the cache
     *
     * @access public
     * @static
     * @return void
     */
    public static function cacheApprove($language_key)
    {
        $db_scheme = (Config::$items['i18n']['db_scheme'] ? Config::$items['i18n']['db_scheme'] . '.' : '');
        Db::query(
            "INSERT INTO {$db_scheme}i18n_cached (id) VALUES (?);",
            [$language_key],
            self::$config['db_config']
        );
    }

    /**
     * Write to cache
     *
     * @access public
     * @static
     * @param  string $language_key Language key
     * @param  object $res Items to set
     * @return void
     */
    public static function cacheWrite($language_key, $res = null)
    {
        if (self::$config['cache'] === 'internal') {
            // Write to internal (file) cache

            $cache_file = self::cacheFile($language_key);
            $contents = "<?php\n\n# Country: " . self::$country_code . "\n# Language: " . self::$language_code . "\n\n";

            // Walk through the result
            foreach ($res as $item) {
                $item['key'] = str_replace("'", "\\'", stripslashes($item['key']));
                $item['value'] = str_replace("'", "\\'", stripslashes($item['value']));
                $contents .= "\$l['{$item['key']}'] = '{$item['value']}';\n";
            }

            // Put contents to the file
            file_put_contents($cache_file, $contents);

            return;
        }

        // Write to external cache (defined by Cache model)
        $cache = [];
        foreach ($res as $item) {
            $cache[$item['key']] = $item['value'];
        }

        Cache::set(self::$cache_key_prefix . $language_key, $cache);
    }

    /**
     * Load from cache
     *
     * @access public
     * @static
     * @return array|bool Returns array of translations
     */
    public static function &cacheRead($language_key)
    {
        $dummy = false;

        // Load from internal (file) cache
        if (self::$config['cache'] === 'internal') {
            $cache_file = self::cacheFile($language_key);

            if (is_file($cache_file) === false) {
                return $dummy;
            }

            require $cache_file;

            if (!isset($l)) {
                return $dummy;
            }

            return $l;
        }

        // Load from external cache (defined by Cache model)
        $res = Cache::get(self::$cache_key_prefix . $language_key);
        if (empty($res) || is_array($res) === false) {
            return $dummy;
        }

        return $res;
    }
}
