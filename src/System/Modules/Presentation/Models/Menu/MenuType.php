<?php

namespace System\Modules\Presentation\Models\Menu;

/**
 * Menu Type
 */

enum MenuType: int
{
    case MAIN_MENU = 100;
    case SUB_MENU = 200;
    case SUB_MENU_NEXT_LEVEL = 201;
    case TABS = 300;
}
