<?php

/*
    You can use file prefixes:
    1. !: - link will be shown as it is
    2. i: - will be used as inline
    3. s: - link will be prepended with site_url
    4. default - link will prepended with base_url
*/

function css()
{
	global $config;

  static $files = array();
  
  if (func_num_args() > 0)
  {
    $tmp = func_get_args();
    $files = array_merge($files, $tmp);
    unset($tmp);
  }
  else
  {
    foreach ($files as $file)
    {
      echo '<style type="text/css">';
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo substr($file, 2);
        break;

        case '!:':
          echo "  @import '". substr($file, 2) ."';  ";
        break;

        case 's:':
					if (isset($config->css_version))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . $config->css_version;
					}
          echo "  @import '". site_url(substr($file, 2)) ."';  ";
        break;

        default:
					if (isset($config->css_version))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . $config->css_version;
					}
          echo "  @import '". base_url($file) ."';  ";
        break;
      }
      echo '</style>';
    }
  }
}


function js()
{
	global $config;

  static $files = array();
  
  if (func_num_args() > 0)
  {
    $tmp = func_get_args();
    $files = array_merge($files, $tmp);
    unset($tmp);
  }
  else
  {
    foreach ($files as $file)
    {
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo '<script type="text/javascript">'. substr($file, 2) .'</script>';
        break;

        case '!:':
          echo '<script type="text/javascript" src="'. substr($file, 2) .'"></script>'."\n";
        break;

        case 's:':
					if (isset($config->js_version))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . $config->js_version;
					}
          echo '<script type="text/javascript" src="'. site_url(substr($file, 2)) .'"></script>'."\n";
        break;

        default:
					if (isset($config->js_version))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . $config->js_version;
					}
          echo '<script type="text/javascript" src="'. base_url($file) .'"></script>'."\n";
        break;
      }
    }
  }
}

?>