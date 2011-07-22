<?php

// Register handlers
if (empty(load::$config['debug']))
{
  set_error_handler('custom_error_handler', E_ALL);
  set_exception_handler('custom_exception_handler');
  register_shutdown_function('send_php_errors');
}

// Array containing errors
$php_errors = array();


// Error handling function
function custom_error_handler($errno, $errstr)
{
  global $php_errors;
  $php_errors[] = func_get_args();
  return FALSE;
}


// Exception handling function
function custom_exception_handler($exception)
{
  global $php_errors;
  $php_errors[] = (array)$exception;
}


// Send php errors on shutdown
function send_php_errors()
{
  global $php_errors;
  if (!empty($php_errors))
  {
    mail('your_email', '!!! PHP error !!!', print_r($php_errors, TRUE). print_r($_SERVER, TRUE), "Content-Type: text/plain; charset=utf-8");
  }
}

?>