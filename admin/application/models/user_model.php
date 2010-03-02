<?php

class user_model
{

  public static function generate_access_list()
  {
    $access_list[] = array('name' => array('all', 'All'));

    foreach (g('config')->access as $item)
    {
      if (is_file(APP_PATH .'controllers/'. $item .'.php'))
      {
        include_once APP_PATH .'controllers/'. $item .'.php';
        $methods = get_class_methods($item);
        if (!empty($methods))
        {
          // Add name to the list
          $tmp =& $access_list[];
          $tmp['name'] = array($item, $item);

          // Sort methods and add to the list
          asort($methods);
          foreach ($methods as $method)
          {
            if ($method[0] != '_')
            {
              $tmp['child'][]['name'] = array($method, $method);
            }
          }
        }
      }
    }

    if (!empty(g('config')->access_callback))
    {
      foreach (g('config')->access_callback as $item)
      {
        if (!class_exists($item['class']))
        {
          load($item['file']);
        }
        if (is_callable(array($item['class'], $item['method'])))
        {
          $access = call_user_func(array($item['class'], $item['method']));
          if (is_array($access))
          {
            $access_list = array_merge($access_list, $access);
          }
        }
      }
    }

    return $access_list;
  }


  static public function print_checkboxes(&$array, $parent = '', $edit = array(), &$checked = false)
  {
  	$html = '';
  	$child_checked = true;
  	foreach ($array as $row)
  	{
  		// Variables
  		$tmp = '';
  		$checked = !empty($edit[$row['name'][0]]);
  		$child_checked = ($checked && $child_checked);
  
  		// Get child elements
  		if (!empty($row['child']) && is_array($row['child']))
  		{
  			$tmp = self::print_checkboxes($row['child'], $parent .'['. $row['name'][0] .']', (empty($edit[$row['name'][0]]) ? null : $edit[$row['name'][0]]), $child_checked);
  			$checked = ($child_checked && $checked);
  		}
  
  		// Create html
  		$html .= '<li>';		
  		$html .= '<input type="checkbox" name="access'. $parent .'['. $row['name'][0] .']" value="1"'.( $checked ? ' checked="checked"' : '' ).' /> '.$row['name'][1];
  		if (!empty($tmp))
  		{
  			$html .= '<ul>';
  			$html .= $tmp;
  			$html .= '</ul>';
  		}
  		$html .= '</li>';
  	}
  	$checked = $child_checked;
  	return $html;
  }


  public static function check_access($class = '', $method = '')
  {
    $class = (empty($class) ? router::$class : $class);
    $method = (empty($method) ? router::$method : $method);

    // Check for user
    if (empty($_SESSION['user']))
    {
      $_SESSION['login_redirect'] = base_url(router::$segments_full_uri);
      router::redirect('login');
    }

    // Check access    
    if (self::_access($class, $method) === false)
    {
      router::error(403, 'Forbidden');
      return false;
    }

    // Else return true
    return true;
  }


  public static function _access()
  {
    $arr = $_SESSION['user']->access;
    
    if (!empty($arr['all']))
    {
      return true;
    }
    
    foreach (func_get_args() as $arg)
    {
    	if (!empty($arr[$arg]))
    	{
    		$arr = $arr[$arg];
    	}
    	else
    	{
    		return false;
    	}
    }
    
    return true;
  }


  public static function login($username, $password)
  {
    if (empty($username))
    {
      return LOGIN_NO_USERNAME;
    }
    elseif (empty($password))
    {
      return LOGIN_NO_PASSWORD;
    }
    else
    {
      $user = db::query("SELECT * FROM `users` WHERE `username` = ?", array($username))->fetch();
      if (empty($user->id))
      {
        return LOGIN_USERNAME_NOT_FOUND;
      }
      elseif (sha1($_POST['password']) != $user->password)
      {
        return LOGIN_WRONG_PASSWORD;
      }
      else
      {
        $user->access = json_decode($user->access, true);
        return $user;
      }
    }
  }
}

?>