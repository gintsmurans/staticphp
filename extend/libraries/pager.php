<?php
/*
  "StaticPHP Framework" - Simple PHP Framework
  
  ---------------------------------------------------------------------------------
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  ---------------------------------------------------------------------------------
  
  Copyright (C) 2009  Gints Murāns <gm@gm.lv>
*/

class pager
{
  private static $obj = null;

  public static function init($record_count, $active_page, $page_limit, $pages_display = 10)
  {
    $pages_left = floor($pages_display / 2);
    $pages_right = $pages_display - $pages_left - 1;
  
    self::$obj = (object)null;
    
    self::$obj->record_count = $record_count;
    self::$obj->active_page = $active_page;
    self::$obj->page_limit = $page_limit;
  
    self::$obj->page_count = ceil(self::$obj->record_count / self::$obj->page_limit);
    if (empty(self::$obj->active_page) || self::$obj->active_page > self::$obj->page_count)
    {
      self::$obj->active_page = 1;
    }
    self::$obj->limit_from = (self::$obj->active_page < 1 ? 0 : (self::$obj->active_page - 1) * self::$obj->page_limit);
  
    self::$obj->next_page = (self::$obj->active_page + 1 > self::$obj->page_count ? false : self::$obj->active_page + 1);
    self::$obj->prev_page = (self::$obj->active_page - 1 < 1 ? false : self::$obj->active_page - 1);
  
    switch (true)
    {
      case (self::$obj->active_page - $pages_left < 1):
        self::$obj->pages_from = 1;
        self::$obj->pages_to = (self::$obj->active_page + $pages_display >= self::$obj->page_count ? self::$obj->page_count : self::$obj->active_page + ($pages_display - self::$obj->active_page));
      break;
      
      case (self::$obj->active_page + $pages_right >= self::$obj->page_count):
        self::$obj->pages_from = (self::$obj->active_page - $pages_display <= 0 ? 1 : self::$obj->active_page - ($pages_display - (self::$obj->page_count - self::$obj->active_page) - 1));
        self::$obj->pages_to = self::$obj->page_count;
      break;
  
      default:
        self::$obj->pages_from = self::$obj->active_page - $pages_left;
        self::$obj->pages_to = self::$obj->active_page + $pages_right;
      break;
    }

    return self::$obj;
  }
}
  
?>