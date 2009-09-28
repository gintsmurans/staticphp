<?php

class jbrowser
{
  public static $vars = array();

  public static function __construct__()
  {
    user_model::check_access();

    // Load jbrowser config file    
    load_config('jbrowser');
    
   //  print_r(json_encode(array('scope' => 'news', 'sizes' => array(array('width' => 180, 'height' => 180, 'crop' => true), array('width' => 640, 'height' => 320, 'crop' => false)))));

    self::$vars['scopes'] = db::query("SELECT `scope` FROM `images` GROUP BY `scope` ORDER BY `scope`")->fetchAll();
    self::$vars['settings'] = array();

    if (!empty($_REQUEST['settings']))
    {
      $settings = json_decode($_REQUEST['settings']);
      self::$vars['settings'] =& $settings;
      if (!empty($settings->scope))
      {
        self::$vars['files'] = db::query('SELECT * FROM `images` WHERE `scope` = ? AND `related_to` IS NULL', $settings->scope)->fetchAll();
      }
    }
    
    //print_r(self::$vars['settings']);
  }
  

  public static function index()
  {
    load('views/jbrowser/jbrowser', self::$vars);
  }
  
  
  public static function upload_form()
  {
    load('views/jbrowser/upload', self::$vars);
  }


  public static function view_files()
  {
    load('views/jbrowser/browse', self::$vars);
  }


  public static function upload()
  {
    if (fv::ispost('scope') && !empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] == 0)
    {
      $dir = PUBLIC_PATH . g('jbrowser')->img_path . $_POST['scope'] .'/';
      $tmp = explode('.', $_FILES['image']['name']);
      $extension = '.'. end($tmp);
      $filename = time() . rand(0, 99);
      unset($tmp);

      // Make template directory
      if (!is_dir($dir))
      {
        mkdir($dir);
      }

      // Move file to directory and insert record into db
      move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename . $extension);

      load('libraries/image');
      image::open($dir . $filename . $extension);

      // Insert record into db
      db::exec(
        "INSERT INTO `images` SET `scope` = ?, `name` = ?, `date` = NOW(), `filename` = ?, `ext` = ?, `width` = ?, `height` = ?, `summ` = ?", 
        array($_POST['scope'], $filename, $filename, $extension, image::$im['width'], image::$im['height'], image::$im['width'] + image::$im['height'])
      );
      
      $new_id = db::last_insert_id();

      // Resize first thumb      
      $thumb_filename = $filename .'-'. 50 .'x'. 50;
      image::resize(50, 50, true, $dir . $thumb_filename . $extension);

      // If another sizes, resize them
      if (!empty(self::$vars['settings']->sizes))
      {
        foreach (self::$vars['settings']->sizes as $size)
        {
          $thumb_filename = $filename .'-'. $size->width .'x'. $size->height;
          image::resize($size->width, $size->height, $size->crop, $dir . $thumb_filename . $extension);
          list($width, $height) = getimagesize($dir . $thumb_filename . $extension);
          db::exec(
            "INSERT INTO `images` SET `scope` = ?, `name` = ?, `date` = NOW(), `filename` = ?, `ext` = ?, `width` = ?, `height` = ?, `summ` = ?, `related_to` = ?", 
            array($_POST['scope'], $thumb_filename, $thumb_filename, $extension, $width, $height, $width + $height, $new_id)
          );
        }
      }

      image::close();

      echo 'done';
    }
  }


  public static function preview()
  {
    if (fv::ispost('image_id'))
    {
      $tmp = db::query("SELECT * FROM `images` WHERE `id` = ? LIMIT 1", $_POST['image_id'])->fetch();
      $tmp2 = db::query("SELECT * FROM `images` WHERE `related_to` = ? AND `summ` < ? ORDER BY `summ` LIMIT 1", array($tmp->id, $tmp->summ))->fetch();
      
      if (!empty($tmp2))
      {
        self::$vars['image_o'] =& $tmp;
        self::$vars['image'] =& $tmp2;
      }
      elseif (!empty($tmp))
      {
        self::$vars['image_o'] =& $tmp;
        self::$vars['image'] =& $tmp;
      }
      else
      {
        return '';
      }

      load('views/jbrowser/preview', self::$vars);
    }
  }
}

?>