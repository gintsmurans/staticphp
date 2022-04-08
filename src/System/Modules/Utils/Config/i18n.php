<?php

// Redirect to default if language is not found
$config['i18n']['redirect'] = true;

// Url format to look for
$config['i18n']['url_format'] = '{{country}}-{{language}}';

// All available countries and languages
// First match will be used, if there is no match, first item in array will be used
$config['i18n']['available'] = [
    [
        'name' => 'Latvia',
        'code' => 'lv',
        'languages' => ['lv', 'en', 'ru'],
        'formats' => [
            'date_short' => 'Y.m.d',
            'date_long' => 'Y.m.d',
            'time' => 'hh:mm:ss',
            'decimal_point' => ',',
            'tousands_separator' => ' ',
            'after_comma' => 2
        ]
    ],
    [
        'name' => 'Estonia',
        'code' => 'ee',
        'languages' => ['ee', 'en', 'ru'],
        'formats' => [
            'date_short' => 'Y.m.d',
            'date_long' => 'Y.m.d',
            'time' => 'hh:mm:ss',
            'decimal_point' => ',',
            'tousands_separator' => ' ',
            'after_comma' => 2
        ]
    ]
];

/**
 * Merge some new country and language prefixes
 */
$config['url_prefixes'] = array_merge($config['url_prefixes'], ['lv-lv', 'lv-ru', 'lv-en', 'ee-ee', 'ee-ru', 'ee-en']);


/**
 *  Cache to use. Possible values: internal or external (uses Cache class)
 */
$config['i18n']['cache'] = 'external';

/**
 *  Cache to use. Possible values: internal or external (uses Cache class)
 */
$config['i18n']['cache_prefix'] = 'language_';

/**
 *  Subdirectory to use for internal cache, will suffix to App's cache dir
 */
$config['i18n']['cache_subdir'] = 'i18n';

/**
 *  String containing db config string used in Config/Db.php
 */
$config['i18n']['db_config'] = 'default';

// Which db scheme to use?
$config['i18n']['db_scheme'] = 'public';
