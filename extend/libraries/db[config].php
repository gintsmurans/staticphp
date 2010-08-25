<?php

// Database configuration using pdo extension, See PDO documentation: http://php.net/manual/en/pdo.construct.php for more information
$config['db']['default'] = array(
  'string' => 'mysql:host=localhost;dbname=',
  'username' => '',
  'password' => '',
  'charset' => 'UTF8',
  'persistent' => TRUE,
);


// String showing which DB configuration to load by default, set to FALSE or NULL if not needed
$config['db']['autoload'] = FALSE;

?>