<?php

$config['version'] = 'v1.2.5';
$config['git_commit_hash'] = 'd985e07d48905edf6d8633c4afaf66afb5031eb6';
$config['git_commit_date'] = '11.12.2023 21:58';

$config['asset_version'] = (
    $config['environment'] === 'dev'
    ? time()
    : substr($config['git_commit_hash'], 0, 7)
);
