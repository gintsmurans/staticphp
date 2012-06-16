<?php

// Set microtime
$microtime = microtime(true);


// Re-Define DS as DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);


// Load all core clases
include BASE_PATH . 'system/load.php'; // Load
include BASE_PATH . 'system/router.php'; // Router


// Load default config file and routing
\load::config(array('config', 'routing'));


// Set debug
\load::$config['debug'] = (\load::$config['debug'] || in_array(\load::$config['client_ip'], (array) \load::$config['debug_ips']));
ini_set('error_reporting', E_ALL);
ini_set('display_errors', (int)\load::$config['debug']);


// Autoload additional config files
if (!empty(\load::$config['autoload_configs']))
{
  \load::config(\load::$config['autoload_configs']);
}


// Define our own error handler
if (!empty(\load::$config['debug']))
{
  function sp_handle_errors($errno , $errstr = NULL, $errfile = NULL, $errline = NULL, $errcontext = NULL)
  {
    if (is_object($errno) === FALSE)
    {
      echo '<p style="background: #FFFFFF; color: #424242;">', "{$errstr}<br />", "{$errfile} (line {$errline})<br />";
      if (!empty($errcontext))
      {
        print_r($errcontext, TRUE);
      }
      echo '</p>';
    }
    else
    {
      $error = $errno->getMessage() . '<br />'. $errno->getFile() .' (line '. $errno->getLine() .')<br /><br />';

      foreach ($errno->getTrace() as $call)
      {
        $error .= $call['file'] .' (line '. $call['line'] .')<br />';
      }

      \router::error('500', 'Internal Server Error', array('error' => $error));
    }
  }

  set_error_handler('sp_handle_errors', E_ALL);
  set_exception_handler('sp_handle_errors');
}


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