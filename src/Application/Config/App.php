<?php

$config['version'] = 'v1.2.2';
$config['git_commit_hash'] = '9527d69aeb043fcd441d9c1a4bc20e3955a8c74c';
$config['git_commit_date'] = '11.12.2023 21:50';

$config['asset_version'] = (
    $config['environment'] === 'dev'
        ? time()
        : substr($config['git_commit_hash'], 0, 7)
);
