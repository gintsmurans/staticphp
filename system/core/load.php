<?php

namespace core;

class LogLevel
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
}


class load
{
    public static $config = [];

    protected static $started_timers = [];
    protected static $finished_timers = [];

    protected static $logs = [];


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
    | Filesystem Methods
    |--------------------------------------------------------------------------
    */

    // Generate UUID v4. Taken from here: http://php.net/manual/en/function.uniqid.php#94959
    public static function uuid4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }


    // Return random hash
    public static function randomHash()
    {
        return sha1(self::uuid4());
    }


    // Return full path to a file
    public static function hashedPath($filename, $randomize = false, $create_directories = false, $levels_deep = 2, $directory_name_length = 2)
    {
        // Explode path to get filename
        $parts = explode(DIRECTORY_SEPARATOR, $filename);

        // Predefine array elements
        $data['hash_dir'] = '';
        $data['hash_file'] = '';

        // Get filename and extension
        $data['filename'] = explode('.', array_pop($parts));
        $data['ext'] = (count($data['filename']) > 1 ? array_pop($data['filename']) : '');
        $data['filename'] = (empty($randomize) ? implode('.', $data['filename']) : self::randomHash());

        if (strlen($data['filename']) < $levels_deep * $directory_name_length)
        {
            throw new Exception('Filename length too small to satisfy how much sub-directories and how long each directory name should be made.');
        }

        // Put directory together
        $dir = (empty($parts) ? '' : implode('/', $parts).'/');

        // Create hashed directory
        for ($i = 1; $i <= $levels_deep; ++$i)
        {
            $data['hash_dir'] .= substr($data['filename'], -1 * $directory_name_length * $i, $directory_name_length).'/';
        }

        // Put other stuff together
        $data['dir'] = str_replace($data['hash_dir'], '', $dir).$data['hash_dir'];
        $data['file'] = $data['dir'].$data['filename'].(empty($data['ext']) ? '' : '.'.$data['ext']);
        $data['hash_file'] = $data['hash_dir'].$data['filename'].(empty($data['ext']) ? '' : '.'.$data['ext']);

        // Create directories
        if (!empty($create_directories) && !is_dir($data['dir']))
        {
            mkdir($data['dir'], 0777, true);
        }

        return $data;
    }


    public static function deleteHashedFile($filename)
    {
        $path = self::hashedPath($filename);

        // Trim off / from end
        $path['hash_dir'] = rtrim($path['hash_dir'], '/');
        $path['dir'] = rtrim($path['dir'], '/');

        // Explode hash directories to get the count of them
        $expl = explode('/', $path['hash_dir']);

        // Unlink the file
        if (is_file($path['file']))
        {
            unlink($path['file']);
        }

        // Remove directories
        foreach ($expl as $null)
        {
            if (!@rmdir($path['dir']))
            {
                break;
            }

            $path['dir'] = dirname($path['dir']);
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
            require (empty($project1) ? APP_PATH : BASE_PATH.$project1.DS).'config'.DS.$name.'.php';
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
            require (empty($project1) ? APP_PATH : BASE_PATH.$project1.DS).'controllers'.DS.$name.'.php';
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
            require (empty($project1) ? APP_PATH : BASE_PATH.$project1.DS).'models'.DS.$name.'.php';
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
            require (empty($project1) ? APP_PATH : BASE_PATH.$project1.DS).'helpers'.DS.$name.'.php';
        }
    }



    /*
    |--------------------------------------------------------------------------
    | Timer methods
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
        self::$finished_timers['*'.$name] = round(microtime(true) - $microtime, 5);
    }


    public static function executionTime()
    {
        global $microtime;

        self::info('Total execution time: '.round(microtime(true) - $microtime, 5)." seconds;");
        self::info('Memory used: '.round(memory_get_usage() / 1024 / 1024, 4)." MB;\n");

        if (!empty(self::$finished_timers))
        {
            krsort(self::$finished_timers);
            foreach (self::$finished_timers as $key => $value)
            {
                self::info("[{$value}s] {$key}");
            }
        }
    }



    /*
    |--------------------------------------------------------------------------
    | Logger methods
    |--------------------------------------------------------------------------
    */

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function emergency($message, array $context = array())
    {
        self::log(LogLevel::EMERGENCY, $message, $context);
    }


    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function alert($message, array $context = array())
    {
        self::log(LogLevel::ALERT, $message, $context);
    }


    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function critical($message, array $context = array())
    {
        self::log(LogLevel::CRITICAL, $message, $context);
    }


    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function error($message, array $context = array())
    {
        self::log(LogLevel::ERROR, $message, $context);
    }


    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function warning($message, array $context = array())
    {
        self::log(LogLevel::WARNING, $message, $context);
    }


    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function notice($message, array $context = array())
    {
        self::log(LogLevel::NOTICE, $message, $context);
    }


    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function info($message, array $context = array())
    {
        self::log(LogLevel::INFO, $message, $context);
    }


    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function debug($message, array $context = array())
    {
        self::log(LogLevel::DEBUG, $message, $context);
    }


    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function log($level, $message, array $context = array())
    {
        self::$logs[] = ['level' => $level, 'message' => $message, 'context' => $context];
    }


    /*
    |--------------------------------------------------------------------------
    | Debug Output
    |--------------------------------------------------------------------------
    */

    public static function debugOutput()
    {
        // Log execution time
        self::executionTime();

        // Generate debug output
        $output = '';
        foreach (self::$logs as $item)
        {
            $class = '';
            switch ($item['level'])
            {
                case LogLevel::EMERGENCY:
                case LogLevel::ALERT:
                case LogLevel::CRITICAL:
                    $class = 'danger';
                    break;

                case LogLevel::ERROR:
                case LogLevel::WARNING:
                    $class = 'warning';
                    break;

                case LogLevel::NOTICE:
                case LogLevel::INFO:
                case LogLevel::DEBUG:
                    $class = 'info';
                    break;
            }

            $output .= '<span class="text-'.$class.'">'.strtoupper($item['level']).': </span>';
            $output .= $item['message'];
            $output .= (!empty($item['context']) ? " [".implode(',', $item['context'])."]\n" : "\n");
        }

        // Return it
        return $output;
    }
}


// Autoload models
spl_autoload_register(function ($classname) {
    $classname = str_replace('\\', DS, $classname);
    $classname = ltrim($classname, DS);

    if (is_file(APP_PATH.$classname.'.php'))
    {
        require APP_PATH.$classname.'.php';
    }
    elseif (is_file(SYS_PATH.$classname.'.php'))
    {
        require SYS_PATH.$classname.'.php';
    }
}, true, true);
