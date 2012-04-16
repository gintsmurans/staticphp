<?php

// Set microtime
$microtime = microtime(true);


// Re-Define DS as DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);


// Load all core clases
include BASE_PATH . 'system/load.php'; // Load
include BASE_PATH . 'system/router.php'; // Router


// Load default config file and routing
load::config(array('config', 'routing'));


// Autoload additional config files
if (!empty(load::$config['autoload_configs']))
{
  load::config(load::$config['autoload_configs']);
}


// Set debug
load::$config['debug'] = (load::$config['debug'] || in_array(load::$config['client_ip'], (array) load::$config['debug_ips']));
ini_set('error_reporting', E_ALL);
ini_set('display_errors', (int)load::$config['debug']);


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