<?php
/*
    "StaticPHP Framework" - Little PHP Framework

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

    Copyright (C) 2009  Gints Murāns <gm@mstuff.org>
*/


// DATABASE
$config['db']['string'] = 'mysql:host=localhost;port=;unix_socket=;dbname='; // See PDO documentation: http://php.net/manual/en/pdo.construct.php
$config['db']['username'] = 'root';
$config['db']['password'] = '';
$config['db']['autoload'] = false;



// Debug IP
$config['debug_ip'] = array('::1', '127.0.0.1');

// Client IP, 
$config['client_ip'] = $_SERVER['REMOTE_ADDR'];



// Set debug
$config['debug'] = false;

// Print script execution time
$config['timer'] = false;



// Set base_url
$config['base_url'] = 'auto';



// Set application and system paths
$config['app_path'] = 'application';
$config['sys_path'] = 'system';


// Set where requested uri and query string will be taken from
$config['request_uri'] =& $_SERVER['REQUEST_URI'];
$config['query_string'] =& $_SERVER['QUERY_STRING'];


// Languages
// Available language prefixes
$config['languages'] = array('en' => 'english');

// Default language prefix
$config['lang_default_prefix'] = 'en';

// Redirect or not if language is not provided
$config['lang_redirect'] = false;

// Path to language file
// In application directory
$config['language_path'] = 'languages.sq3';


// Autoload
// Loads from application directory without .php extension
// You can put full path/to/file too, only without .php
$config['autoload'] = array('helpers/system');


// Load additional config files
// Loads from public/config directory without .php extension
$config['load_configs'] = array();


// URL prefixes can be useful when for example identifying ajax requests, 
// they will be stored in config variable and can be checked with Router::have_prefix('ajax') === true
// they must be in correct sequence. For example, if url is /ajax/test/en/open/29, where ajax and test is prefixes, 
// then array should look like this - array('ajax', 'test'), anyway url can look like /test/en/open/29, too.
$config['url_prefixes'] = array('ajax');


?>