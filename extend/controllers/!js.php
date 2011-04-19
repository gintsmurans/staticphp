<?php
/*
  Controller for handling javascript file with php
  To use add line below to your routing.php file

    'js/.+?\.js' => 'js/index',

  Change "js/.+?\.js" to your javascript directory!

  !! IMPORTANT !!
  Before start writing php scripts in javascript, uncomment lines below in your .htaccess file (change "^js/.*" to your javascript directory)
  
    # RewriteCond %{REQUEST_FILENAME} -f
    # RewriteRule ^js/.* index.php [L]
*/

class js
{
  public static function index()
  {
  	$filename = implode('/', router::$segments);

    if (!empty(router::$segments[1]) && is_file($filename))
    {
      // Disable timer output
      load::$config['timer'] = FALSE;
      
      // Set header and include js file
      header('Content-type: text/javascript; charset=utf-8');
      include_once($filename);
    }
    else
    {
      router::e404();
    }
  }
}

?>