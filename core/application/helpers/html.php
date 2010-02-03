<?php


function css()
{
  static $files = array();
  
  if (func_num_args() > 0)
  {
    $tmp = func_get_args();
    $files = array_merge($files, $tmp);
    unset($tmp);
  }
  else
  {
    if (!empty(g('config')->css))
    {
      foreach (g('config')->css as $key => $item)
      {
        $key = str_replace('/', '\\/', $key);
        if (preg_match('/'.$key.'/', router::$url))
        {
          $files = array_merge($files, (array) $item);
        }
      }
    }

    foreach ($files as $file)
    {
      echo '<style type="text/css">';
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo substr($file, 2);
        break;

        case 'b:':
          echo "  @import '". base_url(substr($file, 2)) ."';  ";
        break;

        case 's:':
          echo "  @import '". site_url(substr($file, 2)) ."';  ";
        break;

        default:
          echo "  @import '". $file ."';  ";
        break;
      }
      echo '</style>';
    }
  }
}


function js()
{
  static $files = array();
  
  if (func_num_args() > 0)
  {
    $tmp = func_get_args();
    $files = array_merge($files, $tmp);
    unset($tmp);
  }
  else
  {
    if (!empty(g('config')->js))
    {
      foreach (g('config')->js as $key => $item)
      {
        $key = str_replace('/', '\\/', $key);
        if (preg_match('/'.$key.'/', router::$url))
        {
          $files = array_merge($files, (array) $item);
        }
      }
    }

    foreach ($files as $file)
    {
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo '<script type="text/javascript">'. substr($file, 2) .'</script>';
        break;

        case 'b:':
          echo '<script type="text/javascript" src="'. base_url(substr($file, 2)) .'"></script>'."\n";
        break;

        case 's:':
          echo '<script type="text/javascript" src="'. site_url(substr($file, 2)) .'"></script>'."\n";
        break;

        default:
          echo '<script type="text/javascript" src="'. $file .'"></script>'."\n";
        break;
      }
    }
  }
}

?>