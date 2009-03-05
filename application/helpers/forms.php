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