<?php

//tideways_enable(TIDEWAYS_FLAGS_NO_SPANS);

// Define paths
define('PUBLIC_PATH', dirname(__FILE__));

// Bootstrap
$basePath = realpath(dirname(__FILE__) . '/../..');
$systemPath = "{$basePath}/System";
$bootstrapPath = "{$systemPath}/Modules/Core/Helpers/Bootstrap.php";
require $bootstrapPath;

/*
$data = tideways_disable();
file_put_contents(
    sys_get_temp_dir() . "/" . uniqid() . ".yourapp.xhprof",
    serialize($data)
);
*/
