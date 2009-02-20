<?php

class eh
{

    private static $debug = false;

    // Init Error Handler class
    public function init($debug = false)
    {
        set_error_handler(array('eh', 'error'));
        set_exception_handler(array('eh', 'exception'));
        
        if ($debug === true)
        {
        	ini_set('error_reporting', E_ALL);
        	ini_set('display_errors', 1);
        }
        
        self::$debug = $debug;
    }


    public static function exception($e)
    {
        $error = '<strong>Exception: </strong>'.$e->getmessage();
        error_log($error);
        
        die('<pre>'.(self::$debug === true ? $error : 'Exception error! Look in log file for more information.').'</pre>');
    }
    
    
    public static function error($errno, $errmsg, $filename, $linenum, $vars)
    {
        $error = '<strong>Error: </strong>'.$errmsg.'<br /><strong>Filename: </strong>'.$filename.'<br /><strong>Line number: </strong>'.$linenum.'<br />';
        error_log($error);

        die('<pre>'.(self::$debug === true ? $error : 'Error! Look in log file for more information.').'</pre>');
    }

}

?>