<?php

$config['db']['mongo']['default'] = [
    'string' => $_ENV['DB_MONGO_DEFAULT_CNS'],
    'dbname' => $_ENV['DB_MONGO_DEFAULT_DB']
];
$config['db']['mongo']['sessions'] = [
    'string' => $_ENV['DB_MONGO_SESSIONS_CNS'],
    'dbname' => $_ENV['DB_MONGO_SESSIONS_DB']
];
