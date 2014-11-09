<?php

// Define config array
$config = [];


/*
|--------------------------------------------------------------------------
| General
|--------------------------------------------------------------------------
*/

$config['base_uri'] = null; // NULL for auto detect




/*
|--------------------------------------------------------------------------
| Debug
|--------------------------------------------------------------------------
*/

// Set environment
$config['environment'] = (empty($_SERVER['app_env']) || $_SERVER['app_env'] !== 'dev' ? 'live' : 'dev');

// Set debug
$config['debug'] = ($config['environment'] !== 'dev' ? false : true);

// List of ip addresses where debug will be turned on by default
$config['debug_ips'] = ['::1', '127.0.0.1'];

/*
| Send errors to this email address.
|
| * Core will only send error emails when debug is turned off.
| * Emails are sent using php's mail function, if you intend to use this feature, make sure your system is configured to be able to send emails.
*/
$config['debug_email'] = null;


/*
|--------------------------------------------------------------------------
| Web server variables 
|
| Set where various variables will be taken from
| In most cases these should work by default
|--------------------------------------------------------------------------
*/

$config['request_uri'] =& $_SERVER['REQUEST_URI'];
$config['query_string'] =& $_SERVER['QUERY_STRING'];
$config['script_name'] =& $_SERVER['SCRIPT_NAME'];

$config['client_ip'] =& $_SERVER['REMOTE_ADDR'];




/*
|--------------------------------------------------------------------------
| Uris
|
| URL prefixes can be useful when for example identifying ajax requests,
| they will be stored in config variable and can be checked with Router::have_prefix('ajax') === TRUE
| they must be in correct sequence. For example, if url is /ajax/test/en/open/29, where ajax and test is prefixes,
| then array should look like this - ['ajax', 'test'], anyway url can also look like /test/en/open/29.
|--------------------------------------------------------------------------
*/
$config['url_prefixes'] = [];




/*
|--------------------------------------------------------------------------
| Autoload
|
| Place filenames without ".php" extension here to autoload various files and classes
| To auto-load files from other projects as well from system, use ['db' => 'system']
|--------------------------------------------------------------------------
*/

$config['autoload_configs'] = [];
$config['autoload_models'] = [];
$config['autoload_helpers'] = ['system'];




/*
|--------------------------------------------------------------------------
| Hooks
|
| Currently only one is supported
|--------------------------------------------------------------------------
*/

$config['before_controller'] = [];

?>