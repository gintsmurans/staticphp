<?php


function css($file = null)
{
  static $files = array();
  
  if (!empty($file))
  {
    $files[] = $file;
  }elseif ($file === null)
  {
    echo '<style type="text/css">';
    foreach ($files as $file)
    {
      echo "  @import '$file';  ";
    }
    echo "</style>\n";
  }
}


function js($file = null)
{
  static $files = array();
  
  if (!empty($file))
  {
    $files[] = $file;
  }elseif ($file === null)
  {
    foreach ($files as $file)
    {
      echo '<script type="text/javascript" src="'.$file.'"></script>'."\n";
    }
  }
}

?>