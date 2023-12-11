<?php

$config['version'] = 'v1.2.1';
$config['git_commit_hash'] = '26481afe07a7be77f0987ec4e13d4df85803df76';
$config['git_commit_date'] = '11.12.2023 21:02';

$config['asset_version'] = (
    $config['environment'] === 'dev'
        ? time()
        : substr($config['git_commit_hash'], 0, 7)
);
