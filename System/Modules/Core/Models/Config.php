<?php

namespace Core\Models;

/**
 * Core class for loading resources.
 */
class Config
{
    /**
     * Global configuration array of mixed data.
     *
     * (default value: [])
     *
     * @var array
     * @access public
     * @static
     */
    public static $items = [];


    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Configuration Methods
    |-------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Get value from config by $name.
     *
     * Optionally set default value if there are no config value by $key found.
     *
     * @access public
     * @static
     * @param  string     $name
     * @param  mixed|null $default (default: null)
     * @return mixed      Returns mixed data
     */
    public static function &get(String $name, $default = false)
    {
        if (isset(self::$items[$name])) {
            return self::$items[$name];
        } else {
            return $default;
        }
    }

    /**
     * Set configuration value.
     *
     * @access public
     * @static
     * @param  string $name
     * @param  mixed  $value
     */
    public static function set(String $name, $value)
    {
        self::$items[$name] = $value;
    }

    /**
     * Merge configuration values.
     *
     * Merge configuration value by $name with $value. If $overwrite is set to true,
     *  same key values will be overwritten.
     *
     * @access public
     * @static
     * @param  string $name
     * @param  mixed  $value
     * @param  bool   $owerwrite (default: true)
     * @return mixed
     */
    public static function merge($name, $value, $owerwrite = true)
    {
        if (!isset(self::$items[$name])) {
            return (self::$items[$name] = $value);
        }

        switch (true) {
            case is_array(self::$items[$name]):
                if (empty($owerwrite)) {
                    return (self::$items[$name] += $value);
                } else {
                    return (self::$items[$name] = array_merge((array) self::$items[$name], (array) $value));
                }
                break;

            case is_object(self::$items[$name]):
                if (empty($owerwrite)) {
                    return (self::$items[$name] = (object) ((array) self::$items[$name] + (array) $value));
                } else {
                    return (self::$items[$name] = (object) array_merge((array) self::$items[$name], (array) $value));
                }
                break;

            case is_int(self::$items[$name]):
            case is_float(self::$items[$name]):
                return (self::$items[$name] += $value);
                break;

            case is_string(self::$items[$name]):
            default:
                return (self::$items[$name] .= $value);
                break;
        }
    }

    /**
     * Load configuration files.
     *
     * Load configuration files from current application's config directory (APP_PATH/config) or
     * from other application by providing name in $project parameter.
     *
     * @access public
     * @static
     * @param  string|array $files
     * @param  string|null  $project (default: null)
     * @return void
     */
    public static function load($files, $module = null, $project = null)
    {
        Load::config($files, $module, $project, self::$items);
    }
}
