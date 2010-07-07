<?php

// DATABASE
$config['db']['default'] = array(
  'string' => 'mysql:host=localhost;unix_socket=/var/mysql/mysql.sock;dbname=', // See PDO documentation: http://php.net/manual/en/pdo.construct.php
  'username' => 'root',
  'password' => '',
  'charset' => 'UTF8',
  'persistent' => TRUE,
);


// String showing which DB configuration to load by default, set to FALSE or NULL if not needed
$config['db']['autoload'] = FALSE;


// Set base_uri, use 'auto' for auto detection
$config['base_uri'] = 'auto';


// Set debug
$config['debug'] = TRUE;


// Print script execution time
$config['timer'] = TRUE;


// Debug IP
$config['debug_ip'] = array('::1', '127.0.0.1');


// Client IP
$config['client_ip'] =& $_SERVER['REMOTE_ADDR'];


// Set where requested uri and query string will be taken from
// In most cases these values should work by default
$config['request_uri'] =& $_SERVER['REQUEST_URI'];
$config['script_name'] =& $_SERVER['SCRIPT_NAME'];


// URI prefixes can be useful when for example identifying ajax requests, 
// they will be stored in config variable and can be checked with Router::have_prefix('ajax') === TRUE
// they must be in correct sequence. For example, if uri is /ajax/test/en/open/29, where ajax and test is prefixes, 
// then array should look like this - array('ajax', 'test'), anyway uri can also look like /test/en/open/29.
// i18n library uses uri prefixes to deal with languages.
$config['uri_prefixes'] = array('ajax');


// -- Autoload
// Set filenames without ".php" extension

// Loads from application directory
// You can put a full path/to/file too, only without .php
$config['load_files'] = array('helpers/system');


// Load additional config files from public/config directory
$config['load_configs'] = array();


// -- HOOKS
// Currently only one is supported
$config['before_controller'] = array();

?>