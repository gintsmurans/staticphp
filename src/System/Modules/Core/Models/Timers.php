<?php

namespace System\Modules\Core\Models;

use System\Modules\Core\Models\Logger;

/**
 * Timer's class.
 */
class Timers
{
    /**
     * Array for started timers.
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $started_timers = [];

    /**
     * Array for finished timers.
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $finished_timers = [];


    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Timer methods
    |-------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Start timer. Timers are started incrementally, i.e. if two timers are started,
     * first second timer needs to be stopped, then first one.
     *
     * @access public
     * @static
     * @return void
     */
    public static function startTimer()
    {
        self::$started_timers[] = microtime(true);
    }

    /**
     * Stop timer by providing name of the timer.
     *
     * @access public
     * @static
     * @param  string $name
     * @return float  Returns time in microseconds it took timer to execute.
     */
    public static function stopTimer($name)
    {
        self::$finished_timers[$name] = round(microtime(true) - array_pop(self::$started_timers), 5);

        return self::$finished_timers[$name];
    }

    /**
     * Mark time with a name.
     *
     * @access public
     * @static
     * @param  string $name
     * @return float  Returns time in microseconds it took to execute from startup to the time the method was called.
     */
    public static function markTime($name)
    {
        global $microtime;
        self::$finished_timers['*' . $name] = round(microtime(true) - $microtime, 5);

        return self::$finished_timers['*' . $name];
    }

    /**
     * Generate debug output for all timers.
     *
     * @access protected
     * @static
     * @return void
     */
    public static function logTimers()
    {
        global $microtime;

        Logger::info('Total execution time: ' . round(microtime(true) - $microtime, 5) . " seconds;");
        Logger::info('Memory used: ' . round(memory_get_usage() / 1024 / 1024, 4) . " MB;\n");

        if (!empty(self::$finished_timers)) {
            krsort(self::$finished_timers);
            foreach (self::$finished_timers as $key => $value) {
                Logger::info("[{$value}s] {$key}");
            }
        }
    }
}
