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
  static $files = array();
  
  if (func_num_args() > 0)
  {
    $files = $files + func_get_args();
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
          echo "  @import '", substr($file, 2), "';  ";
        break;

        case 's:':
					if (isset(load::$config['css_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . load::$config['css_version'];
					}
          echo "  @import '", router::site_uri(substr($file, 2)), "';  ";
        break;

        default:
					if (isset(load::$config['css_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . load::$config['css_version'];
					}
          echo "  @import '", BASE_URI, $file, "';  ";
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
    $files = $files + func_get_args();
  }
  else
  {
    foreach ($files as $file)
    {
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo '<script type="text/javascript">', substr($file, 2), '</script>';
        break;

        case '!:':
          echo '<script type="text/javascript" src="', substr($file, 2), '"></script>',"\n";
        break;

        case 's:':
					if (isset(load::$config['js_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . load::$config['js_version'];
					}
          echo '<script type="text/javascript" src="', router::site_url(substr($file, 2)), '"></script>', "\n";
        break;

        default:
					if (isset(load::$config['js_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . load::$config['js_version'];
					}
          echo '<script type="text/javascript" src="', BASE_URI, $file, '"></script>',"\n";
        break;
      }
    }
  }
}

?>