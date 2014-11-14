<?php

namespace core;


class load
{
    public static $config = [];

    private static $started_timers = [];
    private static $finished_timers = [];


    /*
    |--------------------------------------------------------------------------
    | Configuration methods
    |--------------------------------------------------------------------------
    */

    # Get config variable
    public static function &get($name, $default = null)
    {
        return (isset(self::$config[$name]) ? self::$config[$name] : $default);
    }



    # Set config variable
    public static function set($name, $value)
    {
        return (self::$config[$name] = $value);
    }



    # Merge config variable
    public static function merge($name, $value, $owerwrite = true)
    {
        if (!isset(self::$config[$name]))
        {
            return (self::$config[$name] = $value);
        }

        switch (true)
        {
            case is_array(self::$config[$name]):
                if (empty($owerwrite))
                {
                    return (self::$config[$name] += $value);
                }
                else
                {
                    return (self::$config[$name] = array_merge((array)self::$config[$name], (array)$value));
                }
                break;

            case is_object(self::$config[$name]):
                if (empty($owerwrite))
                {
                    return (self::$config[$name] = (object)((array)self::$config[$name] + (array)$value));
                }
                else
                {
                    return (self::$config[$name] = (object)array_merge((array)self::$config[$name], (array)$value));
                }
                break;

            case is_int(self::$config[$name]):
            case is_float(self::$config[$name]):
                return (self::$config[$name] += $value);
                break;

            case is_string(self::$config[$name]):
            default:
                return (self::$config[$name] .= $value);
                break;
        }
    }




    /*
    |--------------------------------------------------------------------------
    | File Loadings
    |--------------------------------------------------------------------------
    */

    # Load config files
    public static function config($files, $project = null)
    {
        $config =& self::$config;
        foreach ((array)$files as $key => $name)
        {
            $project1 = $project;
            if (is_numeric($key) === false)
            {
                $project1 = $name;
                $name = $key;
            }
            require (empty($project1) ? APP_PATH : BASE_PATH . $project1 . DS) . 'config'. DS . $name .'.php';
        }
    }


    # Load controllers
    public static function controller($files, $project = null)
    {
        foreach ((array)$files as $key => $name)
        {
            $project1 = $project;
            if (is_numeric($key) === false)
            {
                $project1 = $name;
                $name = $key;
            }
            require (empty($project1) ? APP_PATH : BASE_PATH . $project1 . DS) . 'controllers'. DS . $name .'.php';
        }
    }


    # Load models
    public static function model($files, $project = null)
    {
        foreach ((array)$files as $key => $name)
        {
            $project1 = $project;
            if (is_numeric($key) === false)
            {
                $project1 = $name;
                $name = $key;
            }
            require (empty($project1) ? APP_PATH : BASE_PATH . $project1 . DS) . 'models'. DS . $name .'.php';
        }
    }


    # Load views
    public static function view($files, &$data = [], $return = false, $project = '')
    {
        static $globals_added = false;

        // Check for global views variables, can be set, for example, by controller's constructor
        if (!empty(self::$config['view_data']))
        {
            $data = (array)$data + (array)self::$config['view_data'];
        }

        // Add default view data
        if (empty($globals_added))
        {
            load::$config['view_engine']->addGlobal('base_url', router::$base_url);
            load::$config['view_engine']->addGlobal('config', self::$config);
            load::$config['view_engine']->addGlobal('class', router::$class);
            load::$config['view_engine']->addGlobal('method', router::$method);
            load::$config['view_engine']->addGlobal('segments', router::$segments);
            $globals_added = true;
        }

        // Load view data
        $contents = '';
        foreach ((array)$files as $key => $file)
        {
            $contents .= load::$config['view_engine']->render($file, (array)$data);
        }

        // Output or return view data
        if (empty($return))
        {
            echo $contents;
            return true;
        }
        return $contents;
    }


    # Helpers
    public static function helper($files, $project = null)
    {
        foreach ((array)$files as $key => $name)
        {
            $project1 = $project;
            if (is_numeric($key) === false)
            {
                $project1 = $name;
                $name = $key;
            }
            require (empty($project1) ? APP_PATH : BASE_PATH . $project1 . DS) . 'helpers' . DS . $name .'.php';
        }
    }



    /*
    |--------------------------------------------------------------------------
    | Aditional methods
    |--------------------------------------------------------------------------
    */

    public static function startTimer()
    {
        self::$started_timers[] = microtime(true);
    }

    public static function stopTimer($name)
    {
        self::$finished_timers[$name] = round(microtime(true) - array_shift(self::$started_timers), 5);
        return self::$finished_timers[$name];
    }

    public static function markTime($name)
    {
        global $microtime;
        self::$finished_timers['*' . $name] = round(microtime(true) - $microtime, 5);
    }

    public static function executionTime()
    {
        global $microtime;
        $output = 'Total execution time: ' . round(microtime(true) - $microtime, 5) . " seconds;\n";
        $output .= 'Memory used: ' . round(memory_get_usage() / 1024 / 1024, 4) . " MB;\n";

        if (!empty(self::$finished_timers))
        {
            krsort(self::$finished_timers);
            foreach (self::$finished_timers as $key => $value)
            {
                $output .= "\n[{$value}s] {$key}";
            }
        }

        return $output;
    }
}


// Autoload models
spl_autoload_register(function($classname){
    $classname = str_replace('\\', DS, $classname);
    $classname = ltrim($classname, DS);

    if (is_file(APP_PATH . $classname . '.php'))
    {
        require APP_PATH . $classname . '.php';
    }
    elseif (is_file(SYS_PATH . $classname . '.php'))
    {
        require SYS_PATH . $classname . '.php';
    }
}, true, true);

?>