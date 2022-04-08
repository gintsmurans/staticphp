<?php

// Define paths
define('DS', DIRECTORY_SEPARATOR);

define('PUBLIC_PATH', dirname(dirname(__FILE__)) . DS . 'Public' . DS);
define('APP_PATH', dirname(PUBLIC_PATH) . DS);
define('APP_MODULES_PATH', APP_PATH . 'Modules' . DS);
define('BASE_PATH', dirname(APP_PATH) . DS);
define('SYS_PATH', BASE_PATH . 'System' . DS);
define('SYS_MODULES_PATH', SYS_PATH . 'Modules' . DS);

spl_autoload_register(
    function ($classname) {
        $classname = str_replace('\\', DS, $classname);
        $classname = ltrim($classname, DS);

        if (is_file(APP_MODULES_PATH . $classname . '.php')) {
            require APP_MODULES_PATH . $classname . '.php';
        } elseif (is_file(APP_PATH . $classname . '.php')) {
            require APP_PATH . $classname . '.php';
        } elseif (is_file(SYS_MODULES_PATH . $classname . '.php')) {
            require SYS_MODULES_PATH . $classname . '.php';
        } elseif (is_file(SYS_PATH . $classname . '.php')) {
            require SYS_PATH . $classname . '.php';
        } elseif (is_file(BASE_PATH . $classname . '.php')) {
            require BASE_PATH . $classname . '.php';
        }
    },
    true
);
