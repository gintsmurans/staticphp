<?php

/*
  !!!! NOT A FINAL VERSION, ONLY A DRAFT !!!!
  
  Image manipulation
  Simple usage:

    image::open('file');
    image::resize(300, 400, TRUE, 'file.jpg');
*/

class image
{
  public static $im = NULL;
  private static $memory_limit = NULL;


  public static function open($in)
  {
    if (!is_file($in))
    {
      return FALSE;
    }
    
    // Get image dimensions
    list(self::$im['width'], self::$im['height']) = getimagesize($in);
		
		if (empty(self::$im['width']) || empty(self::$im['height']))
		{
			return FALSE;
		}

    // Set memory limit to 128M
    $memory_limit = ini_get('memory_limit');
    ini_set('memory_limit', '128M');

    // Create image from string
    self::$im['im'] = imagecreatefromstring(file_get_contents($in));
		
		return TRUE;
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
    switch (TRUE)
    {
      case (empty($in) && empty(self::$im)):
      case (!function_exists('image'.$type)):
      case (!empty($in) && self::open($in) == FALSE):
        return FALSE;
      break;

      default:
        call_user_func('image'.$type, self::$im['im'], $out, $quality);
        return TRUE;
      break;
    }
  }


  public static function resize($new_width, $new_height, $crop = FALSE, $stretch = FALSE, $out = NULL)
  {
    if (empty(self::$im))
    {
      return FALSE;
    }
    
    // Set default new image sizes
    $im2_height = $new_height;
    $im2_width = $new_width;
    
    // Crop
    $crop_x = 0;
    $crop_y = 0;

    switch (TRUE)
    {
      case ($crop == FALSE && self::$im['width'] > self::$im['height']):
        $im2_height = ceil($new_width * (self::$im['height'] / self::$im['width']));
      break;

      case ($crop == FALSE && self::$im['width'] < self::$im['height']):
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

		if ($stretch == FALSE && $im2_width > self::$im['width'] && $im2_height > self::$im['height'])
		{
			$im2 = imagecreateTRUEcolor(self::$im['width'], self::$im['height']);
			imagecopyresampled($im2, self::$im['im'], 0, 0, 0, 0, self::$im['width'], self::$im['height'], self::$im['width'], self::$im['height']);
		}
		else
		{
			$im2 = imagecreateTRUEcolor($im2_width - $crop_x, $im2_height - $crop_y);
			imagecopyresampled($im2, self::$im['im'], (-1 * ($crop_x / 2)), (-1 * ($crop_y / 2)), 0, 0, $im2_width, $im2_height, self::$im['width'], self::$im['height']);
		}

    if (!empty($out))
    {
      // Output image to file
      imagejpeg($im2, $out, 100);
    }

    // Destroy image objects
    imagedestroy($im2);

    return TRUE;
  }
}

?>