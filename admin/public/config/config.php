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


// DATABASE
$config['db']['default'] = array(
  'string' => 'mysql:host=localhost;unix_socket=/var/mysql/mysql.sock;dbname=staticphp', // See PDO documentation: http://php.net/manual/en/pdo.construct.php
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
$config['timer'] = false;


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


// Languages
// Default language, set to false if you don't need language support
$config['lang_support'] = true;

// Redirect or not if language is not provided
$config['lang_redirect'] = true;

// Country key - try to match this value to the key in countries/language array. 
// Set to false, if you dont want to check against keys
$config['lang_key'] = false;

// This will be the reference to the active language array
$config['lang_active'] = NULL;

// Available languages
// If defined, languages before this will be ignored
// Keys can be regular expression, but will not be quoted
// First match will be used, if there is no match, first item in array will be used
$config['lang_available'] = array(
	array(
		'key' => '',
    'name' => '',
		'directory' => '',
		'languages' => array('en'),
	),
);



// Autoload
// Put filenames without "php" extension
// --------------- 

// Loads from application directory without .php extension
// You can put full path/to/file too, only without .php
$config['load_files'] = array('helpers/system', 'helpers/html', 'models/user_model', 'libraries/form_validation');


// Load additional config files
// Loads from public/config directory
$config['load_configs'] = array('admin');


// Load default language files
// Loads them from application/languages/$current_language/ directory
$config['load_languages'] = array('admin_base');


// HOOKS
// Hook support is not yet fully supported
#$config['hooks'] = array(
#  'pre_controller' => '',
#  'post_controller' => ''
#);

?>