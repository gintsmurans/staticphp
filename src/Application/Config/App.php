<?php

$config['version'] = 'v1.2.6';
$config['git_commit_hash'] = '7f77b0c055956967ea8af1ab4d7ff5294431d6f6';
$config['git_commit_date'] = '12.12.2023 02:34';

$config['asset_version'] = (
    $config['environment'] === 'dev'
    ? time()
    : substr($config['git_commit_hash'], 0, 7)
);
