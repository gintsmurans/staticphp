<?php

/*
  "StaticPHP Framework" - Simple PHP Framework
  
  ---------------------------------------------------------------------------------
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  ---------------------------------------------------------------------------------
  
  Copyright (C) 2009  Gints Murāns <gm@gm.lv>
*/


// Set microtime
$microtime = microtime(true);


// Define
define('DS', DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', dirname(__FILE__).DS);
define('BASE_PATH', realpath(PUBLIC_PATH.'..'.DS).DS);


// include config files
include PUBLIC_PATH.'config/config.php';
include PUBLIC_PATH.'config/routing.php';


// Define system paths
define('APP_PATH', BASE_PATH.trim($config['app_path'], '/\\').DS);
define('SYS_PATH', BASE_PATH.trim($config['sys_path'], '/\\').DS);


// Autoload additional config files
foreach($config['load_configs'] as $tmp)
{
  include PUBLIC_PATH.'config/'. $tmp .'.php';
}


// Set debug
$config['debug'] = ($config['debug'] || in_array($config['client_ip'], (array) $config['debug_ip']));

// Include & init error handler class
include SYS_PATH.'eh.php';
eh::init($config['debug']);


// Include system default helper
include SYS_PATH.'helper.php';


// Load language config
if ($config['lang_support'] === true)
{
  include APP_PATH . $config['lang_path'] . '/config.php';
  $config['lang_default'] =& $config['languages'][0];
}


// Set config array within g() function
g('config', $config);
unset($config);


// Init database, if autoload === true
if (g('config')->db['autoload'] === true)
{
  include SYS_PATH.'db.php';
  DB::init();
}


// Include router class
include SYS_PATH.'router.php';


// Autoload files from config
foreach(g('config')->load_files as $tmp)
{
  load($tmp);
}


// No need for $tmp variable anymore
unset($tmp);


// Init router
router::init();


// If DEBUG output load time
if (g('config')->timer === true)
{  
  echo '<pre style="border-top: 1px #DDD solid; padding-top: 4px;">';
  echo 'Generated in ', round(microtime(true) - $microtime, 5), ' seconds. Memory: ', round(memory_get_usage() / 1024 / 1024, 4), ' MB';

  if (class_exists('db', false))
  {
    echo 'Queries count: ', db::$query_count , "\n", 'Queries: ', print_r(db::$queries, true) ,'';
  }

  echo '</pre>';
}

?>