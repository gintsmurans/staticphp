<?php

// Define paths
define('PUBLIC_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('APP_PATH', dirname(PUBLIC_PATH) . DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(APP_PATH) . DIRECTORY_SEPARATOR);
define('SYS_PATH', BASE_PATH . 'system' . DIRECTORY_SEPARATOR);

// Load core class
include SYS_PATH . 'core/bootstrap.php'; // Load

?>