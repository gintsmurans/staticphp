<?php

use System\Modules\Core\Models\Config;

if (defined('PUBLIC_PATH') == false) {
    define(
        'PUBLIC_PATH',
        realpath(dirname(__FILE__) . '/../../../..')
        . '/Application/Public'
    );
}

if (defined('APP_PATH') == false) {
    define('APP_PATH', dirname(PUBLIC_PATH));
    define('APP_MODULES_PATH', APP_PATH . '/Modules');
    define('BASE_PATH', dirname(APP_PATH));
    define('SYS_PATH', BASE_PATH . '/System');
    define('SYS_MODULES_PATH', SYS_PATH . '/Modules');

    $vendorPath = BASE_PATH . '/vendor';
    if (is_dir($vendorPath) == false) {
        $vendorPath = realpath(BASE_PATH . '/../vendor');
    }
    define('VENDOR_PATH', $vendorPath);
}

spl_autoload_register(
    function ($classname) {
        $classname = str_replace('\\', '/', $classname);
        $classname = ltrim($classname, '/');

        if (is_file(APP_MODULES_PATH . "/{$classname}.php")) {
            include APP_MODULES_PATH . "/{$classname}.php";
        } elseif (is_file(APP_PATH . "/{$classname}.php")) {
            include APP_PATH . "/{$classname}.php";
        } elseif (is_file(SYS_MODULES_PATH . "/{$classname}.php")) {
            include SYS_MODULES_PATH . "/{$classname}.php";
        } elseif (is_file(SYS_PATH . "/{$classname}.php")) {
            include SYS_PATH . "/{$classname}.php";
        } elseif (is_file(BASE_PATH . "/{$classname}.php")) {
            include BASE_PATH . "/{$classname}.php";
        }
    },
    true
);

// Load composer autoload
if (Config::get('disable_twig') !== true) {
    include_once VENDOR_PATH . '/autoload.php';
}
