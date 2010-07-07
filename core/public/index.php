<?php

// Set microtime
$microtime = microtime(true);


// Define paths
define('DS', DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', dirname(__FILE__) . DS);
define('BASE_PATH', dirname(PUBLIC_PATH) . DS);
define('SYS_PATH', BASE_PATH . 'system' . DS);
define('APP_PATH', BASE_PATH . 'application' . DS);
define('CONFIG_PATH', PUBLIC_PATH . 'config' . DS);


// Include default config file
include CONFIG_PATH . 'config.php';

// Load routing
include CONFIG_PATH . 'routing.php';

// Autoload additional config files
foreach($config['load_configs'] as $tmp)
{
  include CONFIG_PATH . $tmp . '.php';
}


// Set debug
$config['debug'] = ($config['debug'] || in_array($config['client_ip'], (array) $config['debug_ip']));
ini_set('error_reporting', E_ALL);
ini_set('display_errors', (int) $config['debug']);


// Include system default helper
include SYS_PATH . 'helper.php';


// Set config array as object and add within g() function
$config = (object) $config;
g()->config = &$config;


// Init database, if autoload is set
if (!empty($config->db[$config->db['autoload']]))
{
  include SYS_PATH . 'db.php';
  db::init($config->db['autoload'], $config->db[$config->db['autoload']], $config->debug);
}


// Include router class
include SYS_PATH . 'router.php';


// Autoload additional files
foreach($config->load_files as $tmp)
{
  load($tmp);
}


// Init router
router::init();


// Output load time, if allowed
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