<?php

# Error handler
function custom_error_handler($errno, $errstr, $errfile, $errline)
{
    $e = new ErrorException($errstr, 0, $errno, $errfile, $errline);
    custom_exception_handler($e);
    return true;
}


# Shutdown method to find out whether shutdown was because of any fatal error
function fatal_error_shutdown_handler()
{
    $last_error = error_get_last();

    if ($last_error['type'] === E_ERROR || $last_error['type'] === E_PARSE)
    {
        $e = new ErrorException($last_error['message'], 0, 0, $last_error['file'], $last_error['line']);
        custom_exception_handler($e);
    }
}


function custom_exception_handler($exception)
{
    if (function_exists('http_response_code'))
    {
        http_response_code(500);
    }

    if (ini_get('display_errors') == 1)
    {
        echo format_exception($exception);
    }
    else
    {
        send_error_email($exception);
    }
}



# Format exception
function format_exception($e)
{
    $session =& $_SESSION;
    $post = $_POST;

    $message = str_replace("\n", "<br />", $e->getMessage());
    $message .= '<br /><br /><strong>Trace:</strong><br /><table border="0" cellspacing="0" cellpadding="5" style="border: 1px #DADADA solid;"><tr><td style="border-bottom: 1px #DADADA solid;">';
    $message .= str_replace("\n", '</td></tr><tr><td style="border-bottom: 1px #DADADA solid;">', $e->getTraceAsString()) . '</td></tr></table>';

    $session = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($session, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));
    $server = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($_SERVER, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));
    $post = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($post, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));

    return "<strong>Error:</strong><br />{$message}<br /><br /><strong>Server:</strong><br />{$server}<br /><br /><strong>Sesssion Info:</strong><br />{$session}<br /><br /><strong>Post Info:</strong><br />{$post}";
}



# Send error email
function send_error_email($e)
{
    global $config;
    mail($config['system_email'], '!! PHP ERROR !!', format_exception($e), "Content-Type: text/html; charset=utf-8");
}


// Register handlers
set_error_handler('custom_error_handler', E_ALL);
set_exception_handler('custom_exception_handler');
register_shutdown_function('fatal_error_shutdown_handler');

?>