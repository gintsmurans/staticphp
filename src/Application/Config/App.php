<?php

$config['version'] = 'v1.2.4';
$config['git_commit_hash'] = '6bf867adea134fab04c8c33284447afa212237f6';
$config['git_commit_date'] = '11.12.2023 21:57';

$config['asset_version'] = (
    $config['environment'] === 'dev'
        ? time()
        : substr($config['git_commit_hash'], 0, 7)
);
