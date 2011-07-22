<?php

// This is the right place to set various system startup options, for example to return default headers or start a session.

// Send content type and charset header
header('Content-type: text/html; charset=utf-8');


// CLI Access
if (!empty($GLOBALS['argv'][1]))
{
  load::$config['request_uri'] =& $GLOBALS['argv'][1];
}

?>