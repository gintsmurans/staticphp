<?php

namespace Core\Models;

use \Core\Models\Config;


/**
 *  Cache library for various cache backends. Currenty only redis is supported.
 */
class Cache
{
    /**
     *  Array of cache backends.
     *
     * (default value: [])
     *
     * @var array
     * @access private
     * @static
     */
    private static $backends = [];

    /**
     *  Reference to self.
     *
     * (default value: null)
     *
     * @var object
     * @access private
     * @static
     */
    private static $ref = null;


    /**
     *  Get backend configuration.
     *
     * @access private
     * @static
     * @return object
     */
    private static function &getBackend($name)
    {
        if (isset(self::$backends[$name]) === false) {
            self::$backends[$name] = [];
        }
        return self::$backends[$name];
    }

    /**
     *  Init cache.
     *
     * @access public
     * @static
     * @return void
     */
    public static function init($name = 'default', $config = null)
    {
        // Choose caching configuration
        if (empty($config)) {
            // If config is not already loaded, do it now
            if (Config::get('cache') === false) {
                Config::load('Cache', null, 'System');
            }
            $config = Config::$items['cache'][$name];
        }

        // Create a reference to self for easier access to it
        self::$ref = new \ReflectionClass('\\Core\\Models\\Cache');

        // Init cache backend
        $cache = &self::getBackend($name);
        $cache['config'] = &$config;
        $cache['type'] = $config['type'];

        // Call Cacher init method
        $method = $config['type'].'_init';
        self::$ref->getMethod($method)->invokeArgs(null, [&$cache, &$config]);
    }

    /**
     *  Set key and value with ttl (time to live).
     *
     * @access public
     * @static
     * @return bool
     */
    public static function set($key, $value, $ttl = null, $name = 'default')
    {
        $cache = self::getBackend($name);
        if (!empty($cache['type'])) {
            if ($ttl === null && isset($cache['config']['timeout'])) {
                $ttl = $cache['config']['timeout'];
            }

            $method = $cache['type'].'_set';
            $params = [&$cache, $key, $value, $ttl];
            return self::$ref->getMethod($method)->invokeArgs(null, $params);
        }

        return false;
    }

    /**
     *  Get value by key.
     *
     * @access public
     * @static
     * @return mixed
     */
    public static function get($key, $name = 'default')
    {
        $cache = self::getBackend($name);
        if (!empty($cache['type'])) {
            $method = $cache['type'].'_get';
            $params = [&$cache, $key];
            return self::$ref->getMethod($method)->invokeArgs(null, $params);
        }

        return false;
    }

    /**
     *  Remove value by $key from cache.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function remove($key, $name = 'default')
    {
        $cache = self::getBackend($name);
        if (!empty($cache['type'])) {
            $method = $cache['type'].'_remove';
            $params = [&$cache, $key];
            return self::$ref->getMethod($method)->invokeArgs(null, $params);
        }

        return false;
    }


    /*
     * =============================================== Files ============================================================
     */

    /**
     *  File init.
     *
     * @access public
     * @static
     * @return void
     */
    public static function files_init(&$cache)
    {
        $path = $cache['config']['path'];

        if (is_dir($path) === false) {
            mkdir($path, 0777, true);
        }

        if (empty($cache['config']['ext'])) {
            $cache['config']['ext'] = 'cache';
        }
        if (empty($cache['config']['levels'])) {
            $cache['config']['levels'] = 0;
        }
        if (empty($cache['config']['sub_path_length'])) {
            $cache['config']['sub_path_length'] = 2;
        }
    }

    /**
     *  Returns filename to the file.
     *
     * @access public
     * @static
     * @return string
     */
    public static function files_filename($cache, $key, $make_dir = false)
    {
        $key = md5($key);

        $subpath = $cache['config']['path'].'/';
        for ($i = 1; $i <= $cache['config']['levels']; $i++) {
            $subpath .= substr($key, -($i * $cache['config']['sub_path_length']), $cache['config']['sub_path_length']);
            $subpath .= '/';
        }

        if (is_dir($subpath) === false) {
            mkdir($subpath, 0777, true);
        }

        return $subpath.$key.'.'.$cache['config']['ext'];
    }

    /**
     *  Set cached value to file.
     *
     * @access public
     * @static
     * @return void
     */
    public static function files_set($cache, $key, $value, $ttl)
    {
        $filename = self::files_filename($cache, $key, true);

        if (is_array($value)) {
            $value = json_encode($value);
        } else if (is_bool($value) || is_numeric($value) || is_string($value)) {
            $value = json_encode(['cacher___encoded' => $value]);
        } else {
            throw new \Exception('Data type is not yet supported');
        }

        file_put_contents($filename, $value);
    }

    /**
     *  Get cached value from file.
     *
     * @access public
     * @static
     * @return mixed
     */
    public static function files_get($cache, $key)
    {
        $filename = self::files_filename($cache, $key, true);

        if (is_file($filename) === false) {
            return false;
        }

        $contents = file_get_contents($filename);
        $contents = json_decode($contents, true);
        if (isset($contents['cacher___encoded'])) {
            $contents = $contents['cacher___encoded'];
        }

        return $contents;
    }

    /**
     *  Remove cached value.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function files_remove($cache, $key)
    {
        $filename = self::files_filename($cache, $key, true);

        if (is_file($filename) === false) {
            return false;
        }

        return unlink($filename);
    }


    /*
     * =============================================== APC =============================================================
     */

    /**
     *  APC init
     *
     * @access public
     * @static
     * @return void
     */
    public static function apc_init(&$cache)
    {
        // Do nothing
    }

    /**
     *  Set cached value by key using APC.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function apc_set($cache, $key, $value, $ttl)
    {
        return apc_store($key, $value, $ttl);
    }

    /**
     *  Get cached value by key using APC.
     *
     * @access public
     * @static
     * @return mixed
     */
    public static function apc_get($cache, $key)
    {
        return apc_fetch($key);
    }

    /**
     *  Get cached value by key using APC.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function apc_remove($cache, $key)
    {
        return apc_delete($key);
    }


    /*
     * =============================================== Redis ===========================================================
     */

    /**
     *  Init Redis cache
     *
     * @access public
     * @static
     * @return void
     */
    public static function redis_init(&$cache)
    {
        if (empty($cache['link'])) {
            $cache['link'] = new \Redis();
            $cache['link']->connect($cache['config']['hostname'], $cache['config']['port']);
            $cache['link']->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        }
    }

    /**
     *  Set cached value by key using Redis.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function redis_set($cache, $key, $value, $ttl)
    {
        return $cache['link']->set($key, $value);
    }

    /**
     *  Get cached value by key using Redis.
     *
     * @access public
     * @static
     * @return mixed
     */
    public static function redis_get($cache, $key)
    {
        return $cache['link']->get($key);
    }

    /**
     *  Remove cached value by key using Redis.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function redis_remove($cache, $key)
    {
        return $cache['link']->delete($key);
    }


    /*
     * =============================================== Memcached =======================================================
     */

    /**
     *  Init Memcached cache
     *
     * @access public
     * @static
     * @return void
     */
    public static function memcached_init(&$cache)
    {
        if (empty($cache['link'])) {
            // Create link
            $id = empty($cache['config']['persistent_id']) ? null : $cache['config']['persistent_id'];
            $cache['link'] = new \Memcached($id);
            $cache['link']->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

            // Add servers
            if (count($cache['link']->getServerList()) == 0) {
                $cache['link']->addServers([
                    [$cache['config']['hostname'], $cache['config']['port']]
                ]);
            }
        }
    }

    /**
     *  Set cached value by key using Memcached.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function memcached_set($cache, $key, $value, $ttl)
    {
        return $cache['link']->set($key, $value);
    }

    /**
     *  Get cached value by key using Memcached.
     *
     * @access public
     * @static
     * @return mixed
     */
    public static function memcached_get($cache, $key)
    {
        return $cache['link']->get($key);
    }

    /**
     *  Remove cached value by key using Memcached.
     *
     * @access public
     * @static
     * @return bool
     */
    public static function memcached_remove($cache, $key)
    {
        return $cache['link']->delete($key);
    }
}
