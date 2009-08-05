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
    foreach ($files as $file)
    {
      echo "  @import '$file';  ";
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
    foreach ($files as $file)
    {
      echo '<script type="text/javascript" src="'.$file.'"></script>'."\n";
    }
  }
}

?>