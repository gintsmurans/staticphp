<?php

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


// URL prefixes can be useful when for example identifying ajax requests,
// they will be stored in config variable and can be checked with Router::have_prefix('ajax') === TRUE
// they must be in correct sequence. For example, if url is /ajax/test/en/open/29, where ajax and test is prefixes,
// then array should look like this - array('ajax', 'test'), anyway url can also look like /test/en/open/29.
$config['url_prefixes'] = array('ajax');


// -- Autoload
// Set filenames without ".php" extension

// Load additional config files from public/config directory
$config['load_configs'] = array();

// Load libraries, config files with the same name will be loaded automatically
$config['load_libraries'] = array();

// Load helpers from application/helpers
$config['load_helpers'] = array('system');


// -- HOOKS
// Currently only one is supported
$config['before_controller'] = array();

?>