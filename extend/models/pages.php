<?php

namespace models;

class pages
{
  private static $obj = null;
  private static $base_uri = NULL;

  public static function init($record_count, $active_page, $page_limit, $pages_display = 10, $base_uri = NULL)
  {
    self::$base_uri = $base_uri;

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


  public static function display()
  {
    if (empty(self::$obj) || self::$obj->page_count <= 1)
    {
      return '';
    }

    $pages = '<div class="pages">';
    $pages .= '<a href="'. self::$base_uri .'1" class="first"><<</a> ';
    $pages .= '<a href="'. self::$base_uri . self::$obj->prev_page .'" class="previous"><</a> ';

    for ($i = self::$obj->pages_from; $i <= self::$obj->pages_to; ++$i)
    {
      $current = ($i == self::$obj->active_page ? ' class="current_page"' : '');
      $pages .= '<a href="'. self::$base_uri . $i .'"'. $current .'>'. $i . '</a> ';
    }

    $pages .= '<a href="'. self::$base_uri . self::$obj->next_page .'" class="last">></a> ';
    $pages .= '<a href="'. self::$base_uri . self::$obj->page_count .'" class="last">>></a>';
    $pages .= '</div>';

    return $pages;
  }
}

?>