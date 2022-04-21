<?php

// Caching
$config['cache'] = [
    'redis' => [
        'prefix' => null,
        'hostname' => 'redisdb',
        'port' => 6379,
        'database' => 2,
        'timeout' => 0, // 5m * 60s = 300s, 0 - no limit, seconds
    ],
    'memcached' => [
        'prefix' => null,
        'servers' => [
            ['127.0.0.1', 11211],
            ['127.0.0.1', 11211],
        ],
        'persistent_id' => null,
        'timeout' => 1, // Seconds
    ],
    'apcu' => [
        'prefix' => null,
    ],
    'files' => [
        'prefix' => null,
        'path' => APP_PATH . '/Cache/cache',
        'ext' => 'cache',
        'levels' => 2, // Max 32/2 = 16 levels per 2 sub path length
        'sub_path_length' => 2
    ]
];
