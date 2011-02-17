<?php

// Set microtime
$microtime = microtime(true);


// Define paths
define('DS', DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', dirname(__FILE__) . DS);
define('BASE_PATH', dirname(PUBLIC_PATH) . DS);


// Load all core clases
include BASE_PATH . 'base/load.php'; // Load
include BASE_PATH . 'base/router.php'; // Router


// Load config environment defination file
include BASE_PATH . 'config/env.php';
load::$env = $env;


// Load default config and routing
load::config(array('config', 'routing'));


// Autoload additional config files
if (!empty(load::$config['load_configs']))
{
  load::config(load::$config['load_configs']);
}


// Set debug
load::$config['debug'] = (load::$config['debug'] || in_array(load::$config['client_ip'], (array) load::$config['debug_ip']));
ini_set('error_reporting', E_ALL);
ini_set('display_errors', (int) load::$config['debug']);


// Autoload libraries
if (!empty(load::$config['load_libraries']))
{
  load::library(load::$config['load_libraries']);
}

// Autoload helpers
if (!empty(load::$config['load_helpers']))
{
  load::helper(load::$config['load_helpers']);
}


// Init router
router::init();


// Output load time, if allowed
if (!empty(load::$config['timer']) && !empty(load::$config['debug']))
{
  echo '<pre style="border-top: 1px #DDD solid; padding-top: 4px;">';
  echo 'Generated in ', round(microtime(true) - $microtime, 5), ' seconds. Memory: ', round(memory_get_usage() / 1024 / 1024, 4), ' MB';
  echo '</pre>';
}

?>