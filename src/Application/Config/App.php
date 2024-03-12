<?php

$config['version'] = 'v1.2.7';
$config['git_commit_hash'] = '31bc97338fb6f48c3c48e25d41ea5965cfd33e49';
$config['git_commit_date'] = '12.01.2024 16:30';

$config['asset_version'] = (
    $config['environment'] === 'dev'
    ? time()
    : substr($config['git_commit_hash'], 0, 7)
);
