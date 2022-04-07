<?php

use System\Modules\Core\Models\Config;
use System\Modules\Utils\Models\Sessions\SessionsMongoDb;
use System\Modules\Utils\Models\Sessions\SessionsRedis;
use System\Modules\Presentation\Models\Menu\Menu;
use Defaults\Data\MainMenu;

// Send content type and charset header
header('Content-type: text/html; charset=utf-8');

// Set locales
// setlocale(LC_TIME, 'lv_LV.utf8', 'lv_LV.UTF-8');
// setlocale(LC_NUMERIC, 'lv_LV.utf8', 'lv_LV.UTF-8');
// setlocale(LC_CTYPE, 'lv_LV.utf8', 'lv_LV.UTF-8');
// date_default_timezone_set('Europe/Riga');

// Start mongoDB connection
$client = new \MongoDB\Client(Config::$items['db']['mongo']['default']['string']);
Config::$items['mdb_conn'] = $client;
Config::$items['mdb_db'] = $client->{Config::$items['db']['mongo']['default']['dbname']};

// Start session
$mdbSession = new SessionsMongoDb(
    Config::$items['db']['mongo']['sessions']['string'],
    Config::$items['db']['mongo']['sessions']['dbname'],
    'SESSION'
);
$mdbSession->register();
$mdbSession->start();

// register twig functions
Menu::registerTwig();

// Default menu
Menu::registerMenu(new MainMenu());
