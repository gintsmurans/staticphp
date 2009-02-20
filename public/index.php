<?php

// Set microtime
$microtime = microtime(true);


// Define
define('DS', DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', dirname(__FILE__).DS);
define('BASE_PATH', realpath(PUBLIC_PATH.'..'.DS).DS);


// include config file
include_once PUBLIC_PATH.'config.php';


// Define system paths
define('APP_PATH', BASE_PATH.trim($config['app_path'], '/\\').DS);
define('SYS_PATH', BASE_PATH.trim($config['sys_path'], '/\\').DS);


// Include & init error handler class
include_once SYS_PATH.'error_handler.php';
eh::init($config['debug']);



// Include system default helper
include_once SYS_PATH.'helper.php';



// Set config array within g() function
g('config', $config);
unset($config);



// Init database, if autoload === true
if (g('config')->db['autoload'] === true)
{
	include_once SYS_PATH.'db.php';
	DB::init();
}




// Init language class
include_once SYS_PATH.'languages.php';
Languages::init();



// Autoload files from config
foreach(g('config')->autoload as $autoload)
{
    load($autoload);
}



// Include & Init router class
include_once SYS_PATH.'router.php';
router::init();



// If DEBUG output load time
if (g('config')->timer === true)
{
    echo '<pre style="border-top: 1px #DDD solid; padding-top: 4px;">Generated in ', round(microtime(true) - $microtime, 5), ' seconds.<br />Memory: '.round(memory_get_usage() / 1024 / 1024, 4).' Mb<br /><br />'.exec("uptime").'</pre>';
}

?>