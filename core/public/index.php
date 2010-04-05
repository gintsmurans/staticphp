<?php

// Set microtime
$microtime = microtime(true);


// Define
define('DS', DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', dirname(__FILE__) . DS);
define('CONFIG_PATH', dirname(__FILE__) . DS . 'config' . DS);
define('BASE_PATH', dirname(PUBLIC_PATH) . DS);
define('APP_PATH', BASE_PATH . 'application' . DS);
define('SYS_PATH', BASE_PATH . 'system' . DS);


// include config files
include CONFIG_PATH.'config.php';


// Set debug
$config['debug'] = ($config['debug'] || in_array($config['client_ip'], (array) $config['debug_ip']));
ini_set('error_reporting', E_ALL);
ini_set('display_errors', (int) $config['debug']);


// Load routing
include CONFIG_PATH.'routing.php';


// Autoload additional config files
foreach($config['load_configs'] as $tmp)
{
  include CONFIG_PATH.'/'. $tmp .'.php';
}


// Include system default helper
include SYS_PATH.'helper.php';


// Set config array as object and add within g() function
$config = (object) $config;
g()->config = &$config;


// Init database, if autoload is set
if (!empty($config->db['autoload']))
{
  include SYS_PATH.'db.php';
  db::init($config->db['autoload']);
}


// Include router class
include SYS_PATH.'router.php';


// Autoload files from config
foreach($config->load_files as $tmp)
{
  load($tmp);
}


// No need for $tmp variable anymore
unset($tmp);


// Init router
router::init();


// If DEBUG output load time
if (!empty($config->timer))
{  
  echo '<pre style="border-top: 1px #DDD solid; padding-top: 4px;">';
  echo 'Generated in ', round(microtime(true) - $microtime, 5), ' seconds. Memory: ', round(memory_get_usage() / 1024 / 1024, 4), ' MB';

  if (class_exists('db', false))
  {
    echo "\n", 'Queries count: ', print_r(db::$query_count, true), "\n", 'Queries: ', print_r(db::$queries, true), '';
  }

  echo '</pre>';
}

?>