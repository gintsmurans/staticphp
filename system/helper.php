<?php

/*
*
*
*/
function site_url($url = '', $prefix = '', $add_language = 'auto')
{
    $url002 = (!empty($prefix) ? Router::trim_slashes($prefix, true).'/' : '') . ($add_language === true || ($add_language === 'auto' && g('config')->lang_redirect === true) ? g('config')->language.'/' : '');
    return g('config')->base_url . $url002 . Router::trim_slashes($url);
}


/*
*
*
*/
function base_url($url = '')
{
    return g('config')->base_url . Router::trim_slashes($url);
}


/*
*
*
*/
function make_path_string($string)
{
    return str_replace(array('/', '\\'), DS, $string);
}


/*
*
*
*/
function load($file, $vars = array(), $prefix = null, $return = false)
{
    // Make filename
	$file = rtrim(make_path_string($file), DS).'.php';


	// Check for file existance
	switch(true)
	{
		case is_file($file):
			// do nothing
		break;


		case is_file(APP_PATH.$file):
            $file = APP_PATH.$file;
		break;


		default:
			throw new Exception('Can\'t load file: '.$file);
		break;
	}
	

    // Include or return file
    if ($return === true)
    {
        $tmp = file_get_contents($file);
        if (!empty($vars) && is_array($vars))
        {
            $tmp = str_replace(array_keys($vars), $vars, $tmp);
        }

        return $tmp;
    }
    else
    {
        // Extract vars	
        if (!empty($vars) && is_array($vars))
        {
        	if (!empty($prefix))
        	{
        		extract($vars, EXTR_PREFIX_ALL, $prefix);
        	}
        	else
        	{
        		extract($vars);
        	}
        }

        include $file;
    }
}


/*
*
*
*/
function l($id, $replace = array(), $output = true)
{
    // Check if not empty $id
    if (empty($id))
    {
        return false;
    }


// ---- SELECT FROM DB -----
    $result = languages::query("SELECT `".g('config')->language."` FROM `languages` WHERE `ident` = ? LIMIT 1", array($id))->fetch();

    if (!empty($result))
    {
        $text = $result->{g('config')->language};
    }

    // Else insert into languages db as NOT-FOUND
    else
    {
        languages::exec("INSERT INTO `languages` (`scope`, `ident`) VALUES (?, ?)", array('NOT-FOUND', $id));
        $text = $id;
    }
// ---- !SELECT FROM DB -----



// ---- PREPARE -----
    // Replace with some new values, if provided
    if (!empty($replace))
    {
        $text = vsprintf($text, $replace);
    }


    // Replace with predefined values
    $text = str_replace(array('%base_url', '%site_url'), array(base_url(), site_url()), $text);
// ---- !PREPARE -----


    // Output or return
    if ($output === true)
    {
        echo $text;
    }
    else
    {
        return $text;
    }
}



/*
*
*
*/
function &g($var = null, $set = null)
{
	// Our static object
	static $vars;


	// Init vars object
	if ($vars === null)
	{
		$vars = (object)null;
	}

    // Set $var
    if (!empty($var) && empty($vars->{$var}))
    {
        $vars->{$var} = (object)null;
    }

	// Set $set
	if ($set !== null)
	{
		$vars->{$var} = (object)$set;
	}


	// Return	
	if (isset($vars->{$var}))
	{
		return $vars->{$var};
	}
	else
	{
		return $vars;
	}
}

?>