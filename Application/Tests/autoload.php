<?php

// Define paths
define('DS', DIRECTORY_SEPARATOR);

define('PUBLIC_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'Public'.DIRECTORY_SEPARATOR);
define('APP_PATH', dirname(PUBLIC_PATH).DIRECTORY_SEPARATOR);
define('APP_MODULES_PATH', APP_PATH.'Modules'.DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(APP_PATH).DIRECTORY_SEPARATOR);
define('SYS_PATH', BASE_PATH.'System'.DIRECTORY_SEPARATOR);
define('SYS_MODULES_PATH', SYS_PATH.'Modules'.DIRECTORY_SEPARATOR);

spl_autoload_register(
    function ($classname) {
        $classname = str_replace('\\', DIRECTORY_SEPARATOR, $classname);
        $classname = ltrim($classname, DIRECTORY_SEPARATOR);

        if (is_file(APP_MODULES_PATH.$classname.'.php')) {
            require APP_MODULES_PATH.$classname.'.php';
        }
        elseif (is_file(APP_PATH.$classname.'.php')) {
            require APP_PATH.$classname.'.php';
        }
        elseif (is_file(SYS_MODULES_PATH.$classname.'.php')) {
            require SYS_MODULES_PATH.$classname.'.php';
        }
        elseif (is_file(SYS_PATH.$classname.'.php')) {
            require SYS_PATH.$classname.'.php';
        }
        elseif (is_file(BASE_PATH.$classname.'.php')) {
            require BASE_PATH.$classname.'.php';
        }
    },
    true
);
