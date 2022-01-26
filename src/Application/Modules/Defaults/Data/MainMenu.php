<?php

namespace Defaults\Data;

use \Core\Models\Router;
use \Core\Models\Config;
use \Core\Models\Presentation\Menu;
use \Core\Models\Presentation\MenuType;

class MainMenu extends Menu
{
    public function __construct()
    {
        $this->type = MenuType::MAIN_MENU;
        $this->menuList = [
            'example' => [
                'title' => 'Example',
                'url' => '%base_url/defaults/welcome/example',
                'show' => function () {
                    return Config::$items['debug'] == true;
                },
                'active' => function () {
                    return Router::$method == 'example';
                }
            ],
            'test-page' => [
                'title' => 'Test Page',
                'url' => '%base_url/defaults/test/test',
                'show' => function () {
                    return Config::$items['debug'] == true;
                },
                'active' => function () {
                    return Router::$method == 'testMe';
                }
            ],
            'single-pass' => [
                'title' => 'JSON Test Page',
                'url' => '%base_url/defaults/test/test/json',
                'show' => function () {
                    return Config::$items['debug'] == true;
                },
                'active' => function () {
                    return Router::$method == 'testMe';
                }
            ],
        ];
    }
}
