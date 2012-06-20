<?php

namespace html;


/*
|--------------------------------------------------------------------------
| Queue css and js files
|
| To queue a file: \html\css('/js/jquery.js');
| To output a queue: \html\css() or \html\js() without any parameters
|
| Prefix string with "i:" to make it an inline script/style
| Define HTML_CSS_VERSION and/or HTML_JS_VERSION to add appropriate file versioning 
|--------------------------------------------------------------------------
*/

function css()
{
  static $files = array();

  if (func_num_args() > 0)
  {
    $files = array_merge($files, func_get_args());
  }
  else
  {
    foreach ($files as $file)
    {
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo '<style>'. substr($file, 2) .'</style>';
        break;

        default:
          if (defined('HTML_CSS_VERSION'))
          {
            $file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . HTML_CSS_VERSION;
          }
          echo '<link rel="stylesheet" type="text/css" href="', $file, '" />';
        break;
      }
    }
  }
}


function js()
{
  static $files = array();

  if (func_num_args() > 0)
  {
    $files = array_merge($files, func_get_args());
  }
  else
  {
    foreach ($files as $file)
    {
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo '<script type="text/javascript">', substr($file, 2), '</script>', "\n";
        break;

        default:
          if (defined('HTML_JS_VERSION'))
          {
            $file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . HTML_JS_VERSION;
          }
          echo '<script type="text/javascript" src="', $file, '"></script>', "\n";
        break;
      }
    }
  }
}




/*
|--------------------------------------------------------------------------
| Some html form helper functions
|--------------------------------------------------------------------------
*/


// Return html dropdown
function dropdown($items, $selected = NULL, $addons = NULL, $add_empty = FALSE, $as_value = NULL, $as_text = NULL, $grouped = FALSE)
{
  $select = (empty($grouped) ? '<select'. (!empty($addons['#']) ? ' ' . $addons['#'] : '') .'>' : '');

  // Add empty option
  if (!empty($add_empty))
  {
    $value = key($add_empty);
    $select .= '<option value="'. set_input_value($value) .'"';
    if (!empty($addons[$value]))
    {
      $select .= ' '. $addons[$value];
    }
    if (is_array($selected) && in_array($value, $selected) || $selected == $value)
    {
      $select .= ' selected="selected"';
    }
    $select .= '>'. reset($add_empty) .'</option>';
  }

  // Loop through options
  foreach ($items as $value => $text)
  {
    // If grouped dropdown
    if (is_array($text))
    {
      $select .= '<optgroup label="'. $value .'">';
      $select .= dropdown($text, $selected, $addons, FALSE, $as_value, $as_text, TRUE);
      $select .= '</optgroup>';
      continue;
    }

    $value = (empty($as_value) ? $value : $text->{$as_value});
    $text = (empty($as_text) ? $text : $text->{$as_text});

    $select .= '<option value="'. set_input_value($value) .'"';
    if (!empty($addons[$value]))
    {
      $select .= ' ' . $addons[$value];
    }

    if (is_array($selected) && in_array($value, $selected) || $selected == $value)
    {
      $select .= ' selected="selected"';
    }
    $select .= '>'. $text .'</option>';
  }


  if (empty($grouped))
  {
    $select .= '</select>';
  }

  return $select;
}



// Set value for inputs
function set_input_value($value)
{
  return str_replace('"', '&quot;', $value);
}


// Set selected for html select element
function set_selected(&$current, $needle)
{
  // Check in the array
  if (is_array($current))
  {
    return (isset($current[$needle]) || in_array($needle, $current) ? ' selected="selected"' : NULL);
  }

  // Else just compare them
  return ($current == $needle ? ' selected="selected"' : NULL);
}


// Set checked for html checbox elements
function set_checked(&$current, $needle)
{
  // Check in the array
  if (is_array($current))
  {
    return (isset($current[$needle]) || in_array($needle, $current) ? ' checked="checked"' : NULL);
  }

  // Else just compare them
  return ($current == $needle ? ' checked="checked"' : NULL);
}

?>