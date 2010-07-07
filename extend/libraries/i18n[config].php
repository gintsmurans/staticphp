<?php

// Redirect or not if language is not provided
$config['lang_redirect'] = TRUE;

// Redirect if country is not specified
$config['lang_country_redirect'] = FALSE;

// Country key - try to match this value to the key in countries/language array.
// Set to FALSE, if you dont want to check against keys (it will search for the country in URI, anyway)
$config['lang_key'] = FALSE; //$_SERVER['HTTP_HOST'];

// Available languages
// First match will be used, if there is no match, first item in array will be used
// Keys can be up to 50 chars in length if used admin part of the framework
$config['lang_available'] = array(
	'lv' => array(
    'name' => 'Latvia',
		'code' => 'lv',
		'languages' => array('lv', 'en', 'ru'),
		'formats' => array(
			'date_short' => 'Y.m.d',
			'date_long' => 'Y.m.d',
			'time' => 'hh:mm:ss',
			'decimal_point' => ',',
			'tousands_separator' => ' ',
			'after_comma' => 2,
		),
	),
);

?>