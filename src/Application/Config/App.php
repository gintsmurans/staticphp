<?php

$config['version'] = 'v1.2.3';
$config['git_commit_hash'] = '34027b84d80afadd1a03ffc0bc41577fe56000b1';
$config['git_commit_date'] = '11.12.2023 21:54';

$config['asset_version'] = (
    $config['environment'] === 'dev'
        ? time()
        : substr($config['git_commit_hash'], 0, 7)
);
