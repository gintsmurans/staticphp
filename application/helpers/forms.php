<?php


function isget()
{
	// Check if post
    return (strtolower($_SERVER['REQUEST_METHOD']) === 'get');
}

function ispost($isset = array())
{

    // Check if post
    if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post')
    {
        return false;
    }

    // Check if isset indexes in POST data
    if (is_array($isset))
    {
	    foreach($isset as $index)
	    {
	        if (!isset($_POST[$index]))
	        {
	            return false;
	        }
	    }
	}

    return true;
}



function set_value($name, $test = '', $value = true, $select = false, $checked = false)
{
    if (empty($_POST[$name]))
    {
        return false;
    }
    
    switch(true)
    {
        case $value:
            echo ' value="'.(!empty($_POST[$name]) ? $_POST[$name] : '').'"';
        break;

        case $select:
            echo ($_POST[$name] == $test ? ' selected="selected"' : '');
        break;
        
        case $checked:
            echo ($_POST[$name] == $test ? ' checked="checked"' : '');
        break;
        
        default:
            echo $_POST[$name];
        break;
    }
}



function valid_email($address)
{
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
}

?>