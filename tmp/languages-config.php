<?php

// Languages
// Default language, set to false if you don't need language support
$config['lang_support'] = true;

// Redirect or not if language is not provided
$config['lang_redirect'] = true;

// Redirect if country is not specified
$config['lang_country_redirect'] = true;

// Country key - try to match this value to the key in countries/language array. 
// Set to false, if you dont want to check against keys (it will search for the country in URI, anyway)
$config['lang_key'] = false; //$_SERVER['HTTP_HOST'];

// This will be the reference to the active language array
$config['lang_active'] = NULL;

// Available languages
// If defined, languages before this will be ignored
// Keys can be regular expression, but will not be quoted
// First match will be used, if there is no match, first item in array will be used
$config['lang_available'] = array(
	'example.com' => array(
    'name' => 'EXAMPLE',
		'directory' => 'example.com',
		'languages' => array('en', 'lv'),
	),
);


// Load default language files at "startup"
// Loads them from application/languages/$current_language/ directory
$config['lang_load'] = array();


?>