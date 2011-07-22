<?php

// Database configuration, See PDO documentation for connection string: http://php.net/manual/en/pdo.construct.php for more information

$config['db']['pdo']['default'] = array(
  'string' => 'mysql:host=localhost;dbname=',
  'username' => '',
  'password' => '',
  'charset' => 'UTF8',
  'persistent' => TRUE,
  'wrap_column' => '`', // ` - for mysql, " - for postgresql
);

?>