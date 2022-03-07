<?php

namespace System\Modules\Core\Models;

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
    public static array $items = [];


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
    public static function &get(string $name, mixed $default = null): mixed
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
    public static function set(string $name, mixed $value): void
    {
        self::$items[$name] = $value;
    }

    /**
     * Get view data value.
     *
     * Optionally set default value if there are no config value by $key found.
     *
     * @param string     $name    Name of the key
     * @param mixed|null $default (default: null)
     *
     * @access public
     * @static
     * @return mixed Returns mixed data
     */
    public static function &getViewData(string $name, mixed $default = null): mixed
    {
        if (isset(self::$items['view_data'][$name])) {
            return self::$items['view_data'][$name];
        } else {
            return $default;
        }
    }

    /**
     * Set view data configuration value.
     *
     * @param string|array $name  Key name
     * @param mixed        $value Value to set (default: null)
     *
     * @access public
     * @static
     * @return void
     */
    public static function setViewData(string $name, mixed $value = null): void
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                self::$items['view_data'][$key] = $value;
            }
        } else {
            self::$items['view_data'][$name] = $value;
        }
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
    public static function merge(string $name, mixed $value, bool $owerwrite = true): mixed
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
     * @param  array $files
     * @param  string|null  $project (default: null)
     * @return void
     */
    public static function load(array $files, ?string $module = null, ?string $project = null): void
    {
        Load::config($files, $module, $project, self::$items);
    }
}
