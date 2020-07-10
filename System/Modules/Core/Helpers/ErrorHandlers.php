<?php

use \Core\Models\Load;
use \Core\Models\Config;
use \Core\Models\Router;
use \Core\Models\RouterException;

/**
 * StaticPHP's error handler. Turns errors into exceptions and passes on to sp_exception_handler().
 *
 * Stops on @ suppressed errors.
 *
 * @see sp_exception_handler()
 * @access public
 * @param mixed $errno
 * @param mixed $errstr
 * @param mixed $errfile
 * @param mixed $errline
 * @return bool Returns whether the error was handled or not.
 */
function sp_error_handler($errno, $errstr, $errfile, $errline)
{
    // @ used
    if (error_reporting() === 0) {
        return false;
    }

    // Throw all the errors as exceptions, so they can be handled as they should
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

/**
 * StaticPHP's exception handler.
 *
 * If debug mode is on, sends formatted error to browser, otherwise sends error email, if debug email is provided in <i>Config/Config.php</i> file.
 *
 * @access public
 * @param Exception|ErrorException|mixed $exception
 * @return void
 */
function sp_exception_handler($exception)
{
    if ($exception instanceof RouterException) {
        if (!empty(Config::$items['debug'])) {
            Router::error('500', 'Internal Server Error', $exception->getMessage());
        } else {
            Router::error('404', 'Not Found');
        }
    }

    if (function_exists('http_response_code') && headers_sent() === false) {
        http_response_code(500);
    }

    if (!empty(Config::$items['debug'])) {
        echo sp_format_exception($exception);
    } elseif (!empty(Config::$items['send_errors']) && Config::$items['send_errors'] == true) {
        sp_send_error_email($exception);
    }

    exit(10);
}

/**
 * Sends error messages.
 *
 * @see sp_format_exception()
 * @access public
 * @param Exception|ErrorException|mixed $e
 * @return void
 */
function sp_send_error_email($e)
{
    static $last_error = ['time' => 0];

    $e_formatted = sp_format_exception($e, true);
    $debug_email = Config::get('debug_email');
    $email_func = Config::get('email_func');
    if ($debug_email !== false && $email_func !== false && (time() - $last_error['time'] >= 30 || $last_error['exception'] != $e_formatted)) {
        $email_func(
            $debug_email, // To
            'PHP ERROR: "'.$_SERVER['HTTP_HOST'].'"', // Subject
            $e_formatted, // Message
            "Content-Type: text/html; charset=utf-8", // Headers
            'error'
        );
        $last_error['time'] = time();
        $last_error['exception'] = $e_formatted;
    }
}

/**
 * Format exception and add session, server and post information for easier debugging.
 *
 * If $full is set to false, only string containing formatted message is returned.
 *
 * @access public
 * @param Exception|ErrorException|mixed $e
 * @param bool $full (default: false)
 * @return string Returns formatted string of the $e exception
 */
function sp_format_exception($e, $full = false)
{
    // Current time
    $datetime = date('d.m.Y H:i:s');

    // Current url
    $url  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://');
    $url .= (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '[unknown host name]');
    $url .= (!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '[unknown url]');

    // Post information
    $post = (!empty($_POST) ? $_POST : '[]');

    // Message
    $message  = str_replace("\n", "<br />", $e->getMessage());
    $message .= '<br /><br /><strong>Trace:</strong><br /><table border="0" cellspacing="0" cellpadding="5" style="border: 1px #DADADA solid;"><tr><td style="border-bottom: 1px #DADADA solid;">';
    $message .= str_replace("\n", '</td></tr><tr><td style="border-bottom: 1px #DADADA solid;">', $e->getTraceAsString()).'</td></tr></table>';

    // Session
    $session = [];
    if (is_callable('formatSession')) {
        $session = formatSession();
    } elseif (isset($_SESSION)) {
        $session = $_SESSION;
    }
    $session = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($session, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));
    $server = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($_SERVER, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));
    $post = str_replace(array(" ", "\n"), array('&nbsp;', '<br />'), json_encode($post, (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null)));

    // Format message
    if (!empty($full)) {
        return "<strong>META</strong><br />{$datetime}<br /><br /><strong>URL</strong><br />{$url}<br /><br /><strong>Error</strong><br />{$message}<br /><br /><strong>Sesssion Info</strong><br />{$session}<br /><br /><strong>Post Info</strong><br />{$post}<br /><br /><strong>Server</strong><br />{$server}";
    } else {
        return "<pre><strong>Error:</strong><br />{$message}<br /></pre>";
    }
}
