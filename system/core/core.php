<?php

// Set microtime
$microtime = microtime(true);


// Re-Define DS as DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);

// Load all core clases
include SYS_PATH . 'core/load.php'; // Load
include SYS_PATH . 'core/router.php'; // Router


// Load default config file and routing
\load::config(['config', 'routing']);


// Set debug
\load::$config['debug'] = (\load::$config['debug'] || in_array(\load::$config['client_ip'], (array)\load::$config['debug_ips']));
ini_set('error_reporting', E_ALL);
ini_set('display_errors', (int)\load::$config['debug']);


// Autoload additional config files
if (!empty(\load::$config['autoload_configs']))
{
    \load::config(\load::$config['autoload_configs']);
}


// Define our own error handler
function sp_handle_errors($errno , $errstr = null, $errfile = null, $errline = null, $errcontext = null)
{
    if (!empty(\load::$config['debug_callback']))
    {
        call_user_func_array(\load::$config['debug_callback'], func_get_args());
    }

    if (!empty(\load::$config['debug']))
    {
        if (is_object($errno) === false)
        {
            echo '<p style="background: #FFFFFF; color: #424242;">', "{$errstr}<br />", "{$errfile} (line {$errline})<br />";
            if (!empty($errcontext))
            {
                print_r($errcontext, true);
            }
            echo '</p>';
        }
        else
        {
            $error = $errno->getMessage() . '<br />'. $errno->getFile() .' (line '. $errno->getLine() .')<br /><br />';

            foreach ($errno->getTrace() as $call)
            {
                if (isset($call['file']))
                {
                    $error .= $call['file'] .' (line '. $call['line'] .')<br />';
                }
            }

            \router::error('500', 'Internal Server Error', ['error' => $error]);
        }
    }
}

set_error_handler('sp_handle_errors', E_ALL);
set_exception_handler('sp_handle_errors');


// Autoload models
if (!empty(\load::$config['autoload_models']))
{
    \load::model(\load::$config['autoload_models']);
}

// Autoload helpers
if (!empty(\load::$config['autoload_helpers']))
{
    \load::helper(\load::$config['autoload_helpers']);
}


// Init router
\router::init();

?>