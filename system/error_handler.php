<?php
/*
    "Frame" - Little PHP Framework

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