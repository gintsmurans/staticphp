<?php

// Caching
$config['cache'] = [
    'default' => [
        'type' => 'redis', // files | apc | redis | memcached
        'hostname' => '127.0.0.1',
        'port' => 6379,
        'timeout' => null, // 5m * 60s = 300s, null - manual
    ],
    'memc' => [
        'type' => 'memcached',
        'hostname' => '127.0.0.1',
        'port' => 11211,
        'persistent_id' => null,
        'timeout' => null,
    ],
    'apc' => [
        'type' => 'apc',
        'timeout' => 300,
    ],
    'fi' => [
        'type' => 'files',
        'path' => APP_PATH.'/Cache/cache',
        'ext' => 'cache',
        'levels' => 1, // Max 32/2 = 16 levels per 2 sub path length
        'sub_path_length' => 2
    ]
];
