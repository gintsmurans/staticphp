<?php


class mlanguage_admin
{

    public static $fields = array();


    public static function get_languages($add = array())
    {
        $where = '';
        $data = null;

        if (!empty($add) && is_array($add))
        {
            $where = " WHERE ".implode(' = ?, ', array_keys($add)).' = ?';
            $data = array_values($add);
        }
        
        return languages::query("SELECT * FROM `languages`{$where}", $data)->fetchAll();
    }
    
    
    public static function get_scopes()
    {
        return languages::query("SELECT scope FROM `languages` GROUP BY `scope`")->fetchAll();
    }
    
    
    public static function get_columns()
    {
        $columns = array();

        $result = languages::query("PRAGMA table_info(`languages`)")->fetchAll();
        foreach ($result as $tmp)
        {
            $columns[] = $tmp->name;
        }

        self::$fields = $columns;

        array_splice($columns, 0, 3);

        return $columns;
    }
    
    
    public static function set($data, $id)
    {
        // Prepare query
        if ($id == 'new')
        {
            $names = implode(",", array_keys($data));
            $values = implode(",", array_fill(0, count($data), '?'));

            languages::exec("INSERT INTO `languages` ($names) VALUES ($values)", array_values($data));

            return Languages::db_link()->lastInsertId();
        }
        else
        {
            $set = implode(' = ?', array_keys($data)).' = ?';
            $data[] = $id;

            languages::exec("UPDATE `languages` SET $set WHERE `id` = ?", array_values($data));
        }
    }
    
    
    
    public static function delete($id)
    {
        if (!empty($id))
        {
            languages::exec("DELETE FROM `languages` WHERE `id` = ?", array($id));
        }
    }
}


?>