<?php
/*
    "StaticPHP Framework" - Little PHP Framework

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


class DB
{

    private static $db_link;


    public static function &init($config = null)
    {
        if ($config === null)
        {
            $config = g('config')->db;
            $db_link =& self::$db_link;
        }

        // Open new connection to DB
        $db_link = new PDO($config['string'], $config['username'], $config['password']);
        
        // Set encoding
        $db_link->exec("SET NAMES UTF8;");

        // Put PDO into exception error mode
        $db_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Allow buffered queries in  MySQL
        $db_link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);

        return $db_link;
    }


    public static function &db_link()
    {
        if (!empty(self::$db_link))
        {
            return self::$db_link;
        }
    }
    
    
    public static function query($query, $data = null)
    {
        if (!empty($query))
        {
            if (is_null(self::$db_link))
            {
                throw new Exception('No connection to database');
            }
            else
            {
                $prepare = self::$db_link->prepare($query);
                $errorCode = self::$db_link->errorCode();

                // Check if errorCode = empty
                // This is for sqlite as it is not throwing any errors
                if ($errorCode == '00000')
                {
                    $prepare->setFetchMode(PDO::FETCH_OBJ);
                    $prepare->execute($data);
                    return $prepare;
                }
                else
                {
                    $errorInfo = self::$db_link->errorInfo();
                    throw new Exception($errorInfo[2]);
                }

                return $prepare;
            }
        }
    }
    
    
    public static function exec($query, $data = null)
    {
        if (!empty($query))
        {
            if (is_null(self::$db_link))
            {
                throw new Exception('No connection to database');
            }
            else
            {
                // Create null return value
                $prepare = null;

                // Try execute query
                try
                {
                    self::$db_link->beginTransaction();
                        $prepare = self::query($query, $data);
                    self::$db_link->commit();
                }
                catch(PDOException $e)
                {
                    self::$db_link->rollback();
                    throw new Exception($e->getMessage());
                }
                
                return $prepare;
            }
        }
    }

}

?>