<?php

// Redirect or not if language is not provided
$config['lang_redirect'] = true;

// Redirect if country is not specified
$config['lang_country_redirect'] = false;

// Country key - try to match this value to the key in countries/language array.
// Set to false, if you dont want to check against keys (it will search for the country in URI, anyway)
$config['lang_key'] = false; //$_SERVER['HTTP_HOST'];

// Available languages
// First match will be used, if there is no match, first item in array will be used
// Keys can be up to 50 chars in length if used admin part of the framework
$config['lang_available'] = array(
	'' => array(
    'code' => 'lv',
    'name' => 'Latvia',
		'table' => 'i18n',
		'languages' => array('en'),
	),
);

// Load languages at startup
$config['lang_load'] = array();

?>