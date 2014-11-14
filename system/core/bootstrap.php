<?php

use \core\load;
use \core\router;

// Set microtime
$microtime = microtime(true);


// Re-Define DS as DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);

// Load all core clases
require SYS_PATH . 'core/load.php'; // Load


// Load default config file and routing
load::config(['config', 'routing']);


// Set debug
load::$config['debug'] = (load::$config['debug'] || in_array(load::$config['client_ip'], (array)load::$config['debug_ips']));
ini_set('error_reporting', (!empty(load::$config['debug']) ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT));
ini_set('display_errors', (int)load::$config['debug']);


// Autoload additional config files
if (!empty(load::$config['autoload_configs']))
{
    load::config(load::$config['autoload_configs']);
}


// Define our own error handlers
# Error handler
function sp_error_handler($errno, $errstr, $errfile, $errline)
{
    $e = new ErrorException($errstr, 0, $errno, $errfile, $errline);
    sp_exception_handler($e);
    return true;
}


# Shutdown method to find out whether shutdown was because of any fatal error
function sp_error_shutdown_handler()
{
    $last_error = error_get_last();

    if ($last_error['type'] === E_ERROR || $last_error['type'] === E_PARSE)
    {
        $e = new ErrorException($last_error['message'], 0, 0, $last_error['file'], $last_error['line']);
        sp_exception_handler($e);
    }
}


# Exception handler
function sp_exception_handler($exception)
{
    if (function_exists('http_response_code') && headers_sent() === false)
    {
        http_response_code(500);
    }

    if (!empty(load::$config['debug']))
    {
        echo sp_format_exception($exception);
    }
    else
    {
        sp_send_error_email($exception);
    }
}


# Send error email
function sp_send_error_email($e)
{
    if (!empty(load::$config['debug_email']))
    {
        mail(load::$config['debug_email'], 'PHP ERROR: "' . $_SERVER['HTTP_HOST'] .'"', sp_format_exception($e, true), "Content-Type: text/html; charset=utf-8");
    }
}


# Format exception
function sp_format_exception($e, $full = false)
{
    $session =& $_SESSION;
    $post = $_POST;

    $message = str_replace("\n", "<br />", $e->getMessage());
    $message .= '<br /><br /><strong>Trace:</strong><br /><table border="0" cellspacing="0" cellpadding="5" style="border: 1px #DADADA solid;"><tr><td style="border-bottom: 1px #DADADA solid;">';
    $message .= str_replace("\n", '</td></tr><tr><td style="border-bottom: 1px #DADADA solid;">', $e->getTraceAsString()) . '</td></tr></table>';

    $session = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($session, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));
    $server = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($_SERVER, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));
    $post = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($post, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));

    if (!empty($full))
    {
        return "<strong>Error:</strong><br />{$message}<br /><br /><strong>Sesssion Info:</strong><br />{$session}<br /><br /><strong>Post Info:</strong><br />{$post}<br /><br /><strong>Server:</strong><br />{$server}";
    }
    else
    {
        return "<pre><strong>Error:</strong><br />{$message}<br /></pre>";
    }
}


// Register error handlers
set_error_handler('sp_error_handler', (!empty(load::$config['debug']) ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT));
set_exception_handler('sp_exception_handler');
register_shutdown_function('sp_error_shutdown_handler');


// Load twig
if (is_file(BASE_PATH . 'vendor/twig/twig/lib/Twig/Autoloader.php') !== true)
{
    throw new Exception('Twig Not Found! If you installed StaticPHP manually, not using composer, please see README.md to where to place the twig library.');
}

require BASE_PATH . 'vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

load::$config['view_loader'] = new Twig_Loader_Filesystem(APP_PATH.'views');
load::$config['view_engine'] = new Twig_Environment(load::$config['view_loader'], array(
    'cache' => APP_PATH.'cache',
    'debug' => load::$config['debug']
));

// Register default filters and functions
// Site url filter
$filter = new Twig_SimpleFilter('siteUrl', function($url = null){
    return router::siteUrl($url);
});
load::$config['view_engine']->addFilter($filter);

// Site url function
$function = new Twig_SimpleFunction('siteUrl', function($url = null){
    return router::siteUrl($url);
});
load::$config['view_engine']->addFunction($function);

// Start timer function
$function = new Twig_SimpleFunction('startTimer', function(){
    load::startTimer();
});
load::$config['view_engine']->addFunction($function);

// Stop timer function
$function = new Twig_SimpleFunction('stopTimer', function($name){
    load::stopTimer($name);
});
load::$config['view_engine']->addFunction($function);

// Mark time function
$function = new Twig_SimpleFunction('markTime', function($name){
    load::markTime($name);
});
load::$config['view_engine']->addFunction($function);

// Execution time function
$function = new Twig_SimpleFunction('executionTime', function(){
    return load::executionTime();
});
load::$config['view_engine']->addFunction($function);


// Autoload models
if (!empty(load::$config['autoload_models']))
{
    load::model(load::$config['autoload_models']);
}

// Autoload helpers
if (!empty(load::$config['autoload_helpers']))
{
    load::helper(load::$config['autoload_helpers']);
}


// Init router
router::init();

?>