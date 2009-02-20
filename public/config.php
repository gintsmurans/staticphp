<?php

// DATABASE
$config['db']['string'] = 'mysql:host=localhost;port=;unix_socket=;dbname='; // See PDO documentation: http://php.net/manual/en/pdo.construct.php
$config['db']['username'] = 'root';
$config['db']['password'] = '';
$config['db']['autoload'] = false;


// Set debug
$config['debug'] = true;


// Print script execution time
$config['timer'] = false;


// Set base_url
$config['base_url'] = 'AUTO';



// Set application and system paths
$config['app_path'] = 'application';
$config['sys_path'] = 'system';



// Set where requested uri and query string will be taken from
$config['request_uri'] = $_SERVER['REQUEST_URI'];



// Languages
// Available language prefixes
$config['languages'] = array('en' => 'english');

// Default language prefix
$config['lang_default_prefix'] = 'en';

// Redirect or not if language is not provided
$config['lang_redirect'] = false;

// language administrator password. You should use hash instead of function
$config['admin_password'] = sha1('pass');


// Autoload
// Loads from application directory without .php extension
// You can put full path/to/file too, only without .php
$config['autoload'] = array('helpers/system');


// URL prefixes can be useful when for example identifying ajax requests, 
// they will be stored in config variable and can be checked with Router::have_prefix('ajax') === true
// they must be in correct sequence. For example, if url is /ajax/test/en/open/29, where ajax and test is prefixes, 
// then array should look like this - array('ajax', 'test'), anyway url can look like /test/en/open/29, too.
$config['url_prefixes'] = array('ajax');



// Routing, each next item overrides current one
// format: 'regular expression' => '[controller directory / ]controller class name | method name'
// Leave first one for default controller
$config['routing'] = array(

	// Default Controller and Method names
	'' => 'home/index',
	
	
	// Handle javascript files with php
	// Put whatever expression equals for js directory, but remember to change it in .htaccess file too
	// Just comment it out, if you don't want php to handle javascript files
    'js/.+?\.js' => 'js/index',
    
    // Rest of the routing
);


?>