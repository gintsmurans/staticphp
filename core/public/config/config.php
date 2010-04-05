<?php

// DATABASE
$config['db']['default'] = array(
  'string' => 'mysql:host=localhost;unix_socket=/var/mysql/mysql.sock;dbname=cars', // See PDO documentation: http://php.net/manual/en/pdo.construct.php
  'username' => 'root',
  'password' => '',
  'charset' => 'UTF8',
);

$config['db']['other'] = array(
  'string' => 'mysql:host=localhost;port=8889;dbname=other', // See PDO documentation: http://php.net/manual/en/pdo.construct.php
  'username' => 'root',
  'password' => 'root',
  'charset' => 'UTF8',
);

// String showing which DB configuration to load
$config['db']['autoload'] = false;


// Set base_url
$config['base_uri'] = 'auto';


// Set debug
$config['debug'] = true;


// Print script execution time
$config['timer'] = true;


// Debug IP
$config['debug_ip'] = array('::1', '127.0.0.1');


// Client IP, 
$config['client_ip'] =& $_SERVER['REMOTE_ADDR'];


// Set where requested uri and query string will be taken from
// Most cases these values should work by default
$config['request_uri'] =& $_SERVER['REQUEST_URI'];
$config['query_string'] =& $_SERVER['QUERY_STRING'];
$config['script_name'] =& $_SERVER['SCRIPT_NAME'];


// URL prefixes can be useful when for example identifying ajax requests, 
// they will be stored in config variable and can be checked with Router::have_prefix('ajax') === true
// they must be in correct sequence. For example, if url is /ajax/test/en/open/29, where ajax and test is prefixes, 
// then array should look like this - array('ajax', 'test'), anyway url can look like /test/en/open/29, too.
$config['uri_prefixes'] = array('ajax');



// Autoload
// Put filenames without "php" extension
// --------------- 

// Loads from application directory without .php extension
// You can put full path/to/file too, only without .php
$config['load_files'] = array('helpers/system');


// Load additional config files
// Loads from public/config directory
$config['load_configs'] = array();


// Load default language files
// Loads them from application/languages/$current_language/ directory
$config['load_languages'] = array();


// HOOKS
// Hook support is not yet fully supported
#$config['hooks'] = array(
#  'pre_controller' => '',
#  'post_controller' => ''
#);

?>