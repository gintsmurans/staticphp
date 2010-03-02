<?php

class navigation_model
{
  public static $models = array();
  public static $menu_array = array();
  public static $processed_menu_array = array();


  public static function &get_models()
  {
    self::$models = db::query("SELECT * FROM `models` ORDER BY `name`")->fetchAll();
    return self::$models;
  }

  // Get menu array from db
  public static function get_menu_array()
  {
    self::$menu_array = db::query("SELECT * FROM `navigation` WHERE `language` = ? ORDER BY `parent_id`, `sort`", g('config')->language)->fetchAll();
  }


  // Process menu array
  public static function process_menu_array()
  { 
    if (!empty(self::$menu_array))
    {
      $menu_array = self::$menu_array;
      self::$menu_array = array();

      $refs = array();
      $list = array();

      foreach($menu_array as $data)
      {
        $thisref =& $refs[$data->id];
        $thisref = $data;
        self::$menu_array[$data->id] =& $thisref;

        // Decode settings
        if (!empty($thisref->settings))
        {
          $thisref->settings = json_decode($thisref->settings);
        }
        
        if ($data->parent_id == 0)
        {
          $thisref->furl = $thisref->url;
          $list[$data->menu_id][$data->id] =& $thisref;
        }
        else
        {
          $thisref->furl = $refs[$data->parent_id]->furl.'/'.$thisref->url;
          $refs[$data->parent_id]->children[$data->id] =& $thisref;
        }
      }

      self::$processed_menu_array = $list;
    }
  }


  public static function build_recursive_menu($menu_array, &$html)
  {
    foreach($menu_array as $key=>$val)
    {
      $delete = "if(confirm('". NAV_CONFIRM1 ."')){ location.href = '". site_url('navigation/delete_item/'. g('nav')->language .'/'. $val->menu_id .'/'. $val->id) ."'; }";
      
      $html .= '<li'. (empty($val->active) ? ' class="inactive"' : '') .'><span>';
      if (empty($val->title))
      {
        $html .= '<span class="error">!</span>';
      }
      
      // Title
      $html .= '<a href="'. site_url('navigation/index/'. g('nav')->language .'/'. $val->menu_id .'/'. $val->id) .'" class="aslink'. ($val->id == g('nav')->nav_id ? ' active' : '' ) .'" id="menu_item_'.$val->id.'" >'.$val->title.'</a>';
      
      // Edit
      if (user_model::_access('navigation', 'edit'))
      {
        $html .= ' <a href="'. site_url('navigation/edit/'. g('nav')->language .'/'. $val->menu_id .'/'. $val->id) .'" title="'. BASE_EDIT .'"><img src="'. base_url('css/images/pencil.png') .'" /></a>';
      }
      
      // Add sub
      if (user_model::_access('navigation', 'add'))
      {
        $html .= ' <a href="'. site_url('navigation/add/'. g('nav')->language .'/'. $val->menu_id .'/'. $val->id) .'"  title="'. NAV_ADD_SUBMENU .'"><img src="'. base_url('css/images/add.png') .'" /></a>';
      }
      
      // Delete
      if (user_model::_access('navigation', 'delete'))
      {
        $html .= ' <a href="#" class="aslink delete" onclick=" '.$delete.' return false; " title="'. BASE_DELETE .'"><img src="'. base_url('css/images/delete.png') .'" /></a>';
      }

      $html .= '</span>';

      if(isset($val->children))
      {
        $html .= '<ul id="menu_item_ul_'. $val->id .'">';
        self::build_recursive_menu($val->children, $html);
        $html .= '</ul>';
      }
      else
      {
        $html .= '</li>';
      }
    }
  }

  public static function generate_menu_tree()
  {
    $html = '';
    foreach (self::$processed_menu_array as $nr => $menu_array)
    {
      $html .= '<div class="filetree"> <div class="nr">#'. $nr .'</div>';
      $html .= '<ul>';
  
      self::build_recursive_menu($menu_array, $html);
  
      $html .= '<li id="menu_item_li_new_0"><a href="'. site_url('navigation/add/'. g('nav')->language .'/'. $nr .'/0/add') .'"  title="'. BASE_ADD .'"><img src="'. base_url('css/images/add.png') .'" /></a></li>';
      $html .= '</ul></div>';
    }

    return $html;
  }
}

?>