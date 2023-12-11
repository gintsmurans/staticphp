<?php

$config['version'] = 'v1.2.1';
$config['git_commit_hash'] = 'b3ffbfd4e8b6e342252da5fcb33ec7e34773801d';
$config['git_commit_date'] = '11.12.2023 21:43';

$config['asset_version'] = (
    $config['environment'] === 'dev'
        ? time()
        : substr($config['git_commit_hash'], 0, 7)
);
