<?php

// Caching
$config['cache'] = array(
  '' => array(
    'type' => 'apc', // false | files | apc
    'prefix' => 'cache/' . $_SERVER['HTTP_HOST'] .'/',
    'key' => $_SERVER['REQUEST_URI'].(empty($_COOKIE['SESSION']) ? '' : $_COOKIE['SESSION']),
    'methods' => array( 'get', 'head' ),
    'timeout' => 300, // 5m * 60s = 300s
    'files_tmp_path' => sys_get_temp_dir(),
  ),
);

?>