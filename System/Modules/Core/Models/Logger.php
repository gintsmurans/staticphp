<?php

namespace Core\Models;

use \Core\Models\Timers;


/**
 * Core logger class.
 */
class Logger
{
    const errorLevels = [
        'none' => 1000,
        'emergency' => 800,
        'alert' => 700,
        'critical' => 600,
        'error' => 500,
        'warning' => 400,
        'notice' => 300,
        'info' => 200,
        'debug' => 100,
    ];

    const NONE = 'none';
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    /**
     * Array for log entries.
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $logs = [];

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Helpers
    |-------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Compare string error levels
     *
     * @param  string $errorLevel1 Set error level
     * @param  string $errorLevel2 Error level to compare to
     * @return bool
     */
    public static function contains(string $errorLevel1, string $errorLevel2): bool
    {
        if (empty(self::errorLevels[$errorLevel1])) {
            return null;
        }
        $errorLevelInt1 = self::errorLevels[$errorLevel1];

        if (empty(self::errorLevels[$errorLevel2])) {
            return null;
        }
        $errorLevelInt2 = self::errorLevels[$errorLevel2];

        return ($errorLevelInt1 <= $errorLevelInt2);
    }

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Logger methods
    |-------------------------------------------------------------------------------------------------------------------
    */

    /**
     * System is unusable.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function emergency($message, array $context = array())
    {
        self::log(Logger::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function alert($message, array $context = array())
    {
        self::log(Logger::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function critical($message, array $context = array())
    {
        self::log(Logger::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function error($message, array $context = array())
    {
        self::log(Logger::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function warning($message, array $context = array())
    {
        self::log(Logger::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function notice($message, array $context = array())
    {
        self::log(Logger::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function info($message, array $context = array())
    {
        self::log(Logger::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function debug($message, array $context = array())
    {
        self::log(Logger::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public static function log($level, $message, array $context = array())
    {
        self::$logs[] = ['level' => $level, 'message' => $message, 'context' => $context];
    }

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Debug Output
    |-------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Generate debug output.
     *
     * @see Load::emergency()
     * @see Load::alert()
     * @see Load::critical()
     * @see Load::error()
     * @see Load::warning()
     * @see Load::notice()
     * @see Load::info()
     * @access public
     * @static
     * @return string Returns formatted html string of debug information, including timers,
     *          but also custom messages logged using logger interface.
     */
    public static function debugOutput()
    {
        // Log execution time
        Timers::logTimers();

        // Generate debug output
        $output = '';
        foreach (self::$logs as $item) {
            $class = '';
            switch ($item['level']) {
                case Logger::EMERGENCY:
                case Logger::ALERT:
                case Logger::CRITICAL:
                    $class = 'danger';
                    break;

                case Logger::ERROR:
                case Logger::WARNING:
                    $class = 'warning';
                    break;

                case Logger::NOTICE:
                case Logger::INFO:
                case Logger::DEBUG:
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
