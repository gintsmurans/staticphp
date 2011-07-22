<?php

namespace html;

/*
  Queue css and js files

    You can use file prefixes:
    1. !: - link will be shown as it is
    2. i: - will be used as inline
    3. s: - link will be prepended with site_url
    4. default - link will prepended with base_url
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
      echo '<style type="text/css">';
      switch (substr($file, 0, 2))
      {
        case 'i:':
          echo substr($file, 2);
        break;

        case '!:':
          echo "  @import '", substr($file, 2), "';  ";
        break;

        case 's:':
					if (isset(\load::$config['html_css_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . \load::$config['html_css_version'];
					}
          echo "  @import '", router::site_uri(substr($file, 2)), "';  ";
        break;

        default:
					if (isset(\load::$config['html_css_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . \load::$config['html_css_version'];
					}
          echo "  @import '", BASE_URI, $file, "';  ";
        break;
      }
      echo '</style>';
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

        case '!:':
          echo '<script type="text/javascript" src="', substr($file, 2), '"></script>', "\n";
        break;

        case 's:':
					if (isset(\load::$config['html_js_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . \load::$config['html_js_version'];
					}
          echo '<script type="text/javascript" src="', router::site_url(substr($file, 2)), '"></script>', "\n";
        break;

        default:
					if (isset(\load::$config['html_js_version']))
					{
						$file = $file . (strpos($file, '?') !== FALSE ? '&' : '?') . \load::$config['html_js_version'];
					}
          echo '<script type="text/javascript" src="', BASE_URI, $file, '"></script>', "\n";
        break;
      }
    }
  }
}



// Return html dropdown
function dropdown($items, $selected = NULL, $addons = NULL, $add_empty = FALSE, $as_value = NULL, $as_text = NULL, $grouped = FALSE)
{
  $select = (empty($grouped) ? '<select'. (!empty($addons['']) ? ' ' . $addons[''] : '') .'>' : '');

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
function set_selected($current, $needle)
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
function set_checked($current, $needle)
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