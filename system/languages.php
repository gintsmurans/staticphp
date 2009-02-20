<?php

class languages
{

    private static $db_link = null;
    
    
    public static function &db_link()
    {
        if (!empty(self::$db_link))
        {
            return self::$db_link;
        }
    }


    public static function init()
    {
        // Open new connection to DB
        self::$db_link = new PDO('sqlite:'.APP_PATH.'languages/db.sq3');
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
            }
        }
    }
    
    
    public static function exec($query, $data)
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