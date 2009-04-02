<?php
/*
    "StaticPHP Framework" - Little PHP Framework
    
    Form Validation class
    Simple usage:
      
      fv::init($_POST);
      fv::add_rules(array(
        'email' => array(
          'valid' => array('required', 'email'),
          'filter' => array('trim'),
        ),
      ));
      
      // This will print out all errors
      if (fv::validate() == false)
      {
        print_r(fv::$errors_all);
      }
      
      // And html code, this will output first error for "email" field
      <?php if (($test = fv::get_error('email')) != false): ?>
        <div class="error"><?php echo $test[0]; ?></div>
      <?php endif; ?>

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



class fv
{

  public static $errors = null;
  public static $errors_all = null;

  public static $post = array();
  private static $rules = array();
  
  private static $default_errors = array(
    'missing' => 'Field "!name" is missing',
    'required' => 'Field "!name" is required',
    'email' => '"!value" is not a correct e-mail address',
    'date' => '"!value" is not a correct date format',
    'ipv4' => '"!value" is not a correct ipv4 address',
    'ipv6' => '"!value" is not a correct ipv6 address',
    'credit_card' => '"!value" is not a correct credit card number',
    
    'length' => 'Field "!name" has not correct length',
    'equal' => 'Field "!name" has wrong value',
    'format' => 'Field "!name" has not a correct format',
    
    'integer' => 'Field "!name" must be integer',
    'float' => 'Field "!name" must be float number',
    'string' => 'Field "!name" can contain only letters, []$/!.?()-\'" and space chars',
    
    'upload_required' => 'Field "!name" is required',
    'upload_size' => 'Uploaded file is to large',
    'upload_ext' => 'File type is not allowed',
  );




  public static function init()
  {
    foreach (func_get_args() as $item)
    {
      if (is_array($item))
      {
        self::$post = array_merge(self::$post, $item);
      }
    }
  }
  
  
  public static function errors($errors)
  {
    self::$default_errors = array_merge(self::$default_errors, $errors);
  }


  public static function add_rules($rules)
  {
    self::$rules = array_merge(self::$rules, $rules);
  }


  public static function validate()
  {
    foreach (self::$rules as $name => $value)
    {
      if (!isset(self::$post[$name]))
      {
        self::set_error('missing', $name);
      }
      else
      {
        self::filter_field($name);
        self::validate_field($name);
      }
    }

    return empty(self::$errors);
  }


  public static function filter_field($name)
  {
    if (!empty(self::$rules[$name]['filter']))
    {
      foreach (self::$rules[$name]['filter'] as $item)
      {
        $matches = $args = array();
        $call = null;
        
        // Get args from []
        if (preg_match('/(\w+)\[(.*)\]/', $item, $matches))
        {
          $item = $matches[1];
          $args = explode(',', $matches[2]);
          $args = str_replace('&#44;', ',', $args);
        }
        
        // Add value as first argument
        array_unshift($args, self::$post[$name]);
        
        // Call function
        if (($tmp = self::call_func($item, $args)) != false)
        {
          self::$post[$name] = $tmp;
        }
      }
    }
  }


  public static function validate_field($name)
  {
    if (!empty(self::$rules[$name]['valid']))
    {
      foreach (self::$rules[$name]['valid'] as $item)
      {
        $matches = $args = array();
        $call = null;

        // Get args from []
        if (preg_match('/(\w+)\[(.*)\]/', $item, $matches))
        {
          $item = $matches[1];
          $args = explode(',', $matches[2]);
          $args = str_replace('&#44;', ',', $args);
        }
        
        // Add value as first argument
        array_unshift($args, self::$post[$name]);
        
        // Call function
        if (self::call_func($item, $args) === false)
        {
          self::set_error($item, $name, self::$post[$name]);
        }
      }
    }
  }


  public static function set_error($type, $name, $value = '')
  {
    self::$errors_all[] = &$tmp;
    self::$errors[$name][] = &$tmp;

    $tmp = strtr(
      (!empty(self::$rules[$name]['errors'][$type]) ? self::$rules[$name]['errors'][$type] : (empty(self::$default_errors[$type]) ? '' : self::$default_errors[$type])), 
      array('!name' => $name, '!value' => $value)
    );
  }

  public static function get_error($name)
  {
    return (empty(self::$errors[$name]) ? false : self::$errors[$name]);
  }
  
  
  private static function call_func($func, $args = null)
  {
    // Check for callable function
    if (method_exists('fv', $func))
    {
      $call = array('fv', $func);
    }
    elseif (function_exists($func))
    {
      $call = $func;
    }

    // Call method / function
    if (!empty($call))
    {
      return call_user_func_array($call, $args);
    }
  }




  /**
  *
  *   FILTER METHODS
  *
  **/

  public static function xss($string)
  {
    // Decode urls
    $string = rawurldecode($string);
    
    // Escape non ending tags
    $string = preg_replace('#(<)([a-z]+[^>]*(</[a-z]*>|</|$))#iu', '&lt;$2', $string);
    
    // Avoid php tags
    $string = str_ireplace(array("\t", '<?php', '<?', '?>'),  array(' ', '&lt;?php', '&lt;?', '?&gt;'), $string);
    
    // Clean empty tags
    $string = preg_replace('#<(?!input¦br¦img¦hr¦\/)[^>]*>\s*<\/[^>]*>#iu', '', $string);

    $string = str_ireplace(array("&amp;", "&lt;", "&gt;"), array("&amp;amp;", "&amp;lt;", "&amp;gt;"), $string);
    
    // fix &entitiy\n;
    $string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $string);
    $string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $string);

    $string = html_entity_decode($string, ENT_COMPAT, "UTF-8");

    // remove any attribute starting with "on" or xmlns
    $string = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])\ ?(on|xmlns)[^>]*?>#iUu', "$1>", $string);
    
    // remove javascript: and vbscript: protocol
    $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
    $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);
    $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*-moz-binding[\x00-\x20]*:#Uu', '$1=$2nomozbinding...', $string);
    $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*data[\x00-\x20]*:#Uu', '$1=$2nodata...', $string);
    
    //remove any style attributes, IE allows too much stupid things in them, eg.
    //<span style="width: expression(alert('Ping!'));"></span> 
    // and in general you really don't want style declarations in your UGC

    $string = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])(class|lang|style|size|face)[^>]*>#iUu', "$1>", $string);

    //remove namespaced elements (we do not need them...)
    $string = preg_replace('#</*\w+:\w[^>]*>#i', "", $string);
    
    //remove really unwanted tags
    //do {
    //    $oldstring = $string;
        $string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*(>|<|$)#i', "", $string);
    //} while ($oldstring != $string);
    
    return $string;
  }




  /**
  *
  *   VALIDATION METHODS
  *
  **/
  
  public static function required($value)
  {
    return !empty($value);
  }

  public static function email($email)
  {
    return (bool) preg_match("/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/ix", $email);
  }

  public static function date($value, $format = '^(19|20)[0-9]{2}[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$')
  {
    return self::valid_format($value, $format);
  }
  
  public static function ipv4($value)
  {
    return (bool) preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $value);
  }
  
  public static function ipv6($value)
  {
    return (bool) preg_match('/^(^(([0-9A-F]{1,4}(((:[0-9A-F]{1,4}){5}::[0-9A-F]{1,4})|((:[0-9A-F]{1,4}){4}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,1})|((:[0-9A-F]{1,4}){3}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,2})|((:[0-9A-F]{1,4}){2}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,3})|(:[0-9A-F]{1,4}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,4})|(::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,5})|(:[0-9A-F]{1,4}){7}))$|^(::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,6})$)|^::$)|^((([0-9A-F]{1,4}(((:[0-9A-F]{1,4}){3}::([0-9A-F]{1,4}){1})|((:[0-9A-F]{1,4}){2}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,1})|((:[0-9A-F]{1,4}){1}::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,2})|(::[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,3})|((:[0-9A-F]{1,4}){0,5})))|([:]{2}[0-9A-F]{1,4}(:[0-9A-F]{1,4}){0,4})):|::)((25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{0,2})\.){3}(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{0,2})$$/', $value);
  }
  
  public static function credit_card($value)
  {
    $value = preg_replace('/[^0-9]+/', '', $value);
    return (bool) preg_match('/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/', $value);
  }
  
  public static function length($value, $from, $to = null)
  {
    $len = strlen($value);

    switch (true)
    {
      case ($to == '>'):
        return ($len >= $from);
      break;
      
      case ($to == '>'):
        return ($len <= $from);
      break;
      
      case (ctype_digit($to)):
        return ($len >= $from && $len <= $to);
      break;

      case ($to == '='):
      default:
        return ($len == $from);
      break;
    }
  }
  
  public static function equal($value, $equal, $cast = false)
  {
    return ($cast == false ? $value === $equal : $value == $equal);
  }

  public static function format($value, $format = '')
  {
    $format = str_replace('/', '\\/', $format);
    return (bool) preg_match("/$format/", $value);
  }
  
  public static function integer($value)
  {
    return (bool) preg_match('/^\d+$/x', $value);
  }
  
  public static function float($value, $delimiter = '.')
  {
    return (bool) preg_match('/^\d+'.preg_quote($delimiter, '/').'?\d+$/', $value);
  }
  
  public static function string($value)
  {
    return (bool) preg_match('/^[a-z]+$/i', $value);
  }




  public static function upload_required($upload)
  {
    return (is_array($upload) && !empty($upload['name']) && !empty($upload['tmp_name']) && !empty($upload['size']));
  }
  
  public static function upload_size($upload, $size)
  {
    if (self::upload_required($upload))
    {
      return ($upload['size'] <= $size);
    }
  }
  
  public static function upload_ext($upload, $extensions)
  {
    if (self::upload_required($upload))
    {
      $ext = explode(' ', $extensions);
      $tmp = explode('.', $upload['name']);

      return in_array(end($tmp), $ext);
    }
  }




  /**
  *
  *   FORM HELPERS
  *
  **/

  public static function isget()
  {
    return (strtolower($_SERVER['REQUEST_METHOD']) === 'get');
  }
  
  public static function ispost($isset = null)
  {
    // Check if post
    if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post')
    {
      return false;
    }

    // Check if isset keys in POST data
    if ($isset !== null)
    {
      foreach((array) $isset as $key)
      {
        if (!isset(self::$post[$key]))
        {
          return false;
        }
      }
    }
    return true;
  }
  
  
  
  public static function set_input($name)
  {
    if (($field = self::get_field($name)) == false)
    {
      return false;
    }
    echo ' value="'.(!empty($field) ? $field : '').'"';
  }

  public static function set_select($name, $test = '')
  {
    if (($field = self::get_field($name)) == false)
    {
      return false;
    }
    echo ((is_array($field) && in_array($test, $field)) || $field == $test ? ' selected="selected"' : '');
  }


	public static function set_checkbox($name)
	{
		if (($field = self::get_field($name)) == false)
		{
			return false;
		}
		echo (!empty($field) ? ' checked="checked"' : '');
	}


  public static function set_value($name)
  {
    if (($field = self::get_field($name)) == false)
    {
      return false;
    }
    echo $field;
  }
  
  
  private static function get_field($name)
  {
    $field = self::$post;

    foreach ((array)$name as $item)
    {
      if (isset($field[$item]))
      {
        $field =& $field[$item];
      }
      else
      {
        return false;
      }
    }
    return $field;
  }
  
}

?>