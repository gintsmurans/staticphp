<?php
/*
    "StaticPHP Framework" - Little PHP Framework
    
    !!!! NOT A FINAL VERSION, ONLY A DRAFT !!!!
    
    Image manupalution
    Simple usage:
      
      image::open('file');
      image::resize(300, 400, true, 'file.jpg');

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

    Copyright (C) 2009  Gints Murāns <gm@mstuff.org>
*/



class image
{
  public static $im = null;
  private static $memory_limit = null;


  public static function open($in)
  {
    if (!is_file($in))
    {
      return false;
    }
    
    // Try to get image dimensions
    try
    {
      list(self::$im['width'], self::$im['height']) = getimagesize($in);
    }
    catch(Exception $e)
    {
      return false;
    }

    // Set memory limit to 128M
    $memory_limit = ini_get('memory_limit');
    ini_set('memory_limit', '128M');

    // Create image from string
    self::$im['im'] = imagecreatefromstring(file_get_contents($in));
    
    return true;
  }


  public static function close()
  {
    // Destroy image objects
    imagedestroy(self::$im['im']);

    // Set back memory limit
    ini_set('memory_limit', self::$memory_limit);
  }




  public static function resave($in, $out, $type = 'jpeg', $quality = '100')
  {
    switch (true)
    {
      case (empty($in) && empty(self::$im)):
      case (!function_exists('image'.$type)):
      case (!empty($in) && self::open($in) == false):
        return false;
      break;

      default:
        call_user_func('image'.$type, self::$im['im'], $out, $quality);
        return true;
      break;
    }
  }


  public static function resize($new_width, $new_height, $crop = false, $out = null)
  {
    if (empty(self::$im))
    {
      return false;
    }
    
    // Set default new image sizes
    $im2_height = $new_height;
    $im2_width = $new_width;
    
    // Crop
    $crop_x = 0;
    $crop_y = 0;

    switch (true)
    {
      case ($crop == false && self::$im['width'] > self::$im['height']):
        $im2_height = ceil($new_width * (self::$im['height'] / self::$im['width']));
      break;

      case ($crop == false && self::$im['width'] < self::$im['height']):
        $im2_width = ceil($new_height * (self::$im['width'] / self::$im['height']));
      break;

      case (self::$im['width'] > self::$im['height']):
        $im2_width = ceil($new_height * (self::$im['width'] / self::$im['height']));
        $crop_x = ($im2_width - $new_width);
      break;

      case (self::$im['width'] < self::$im['height']):
        $im2_height = ceil($new_width * (self::$im['height'] / self::$im['width']));
        $crop_y = ($im2_height - $new_height);
      break;
    }

    // Create image handler for resized picture
    $im2 = imagecreatetruecolor($im2_width - $crop_x, $im2_height - $crop_y);

    imagecopyresampled($im2, self::$im['im'], (-1 * ($crop_x / 2)), (-1 * ($crop_y / 2)), 0, 0, $im2_width, $im2_height, self::$im['width'], self::$im['height']);

    if (!empty($out))
    {
      // Output image to file
      imagejpeg($im2, $out, 100);
    }
    else
    {
    
    }
    
    
    // Destroy image objects
    imagedestroy($im2);

    return true;
  }
}

?>