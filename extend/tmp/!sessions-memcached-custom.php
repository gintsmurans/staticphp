<?php

/*
  -- THIS IS VERY SPECIFIC LIBRARY AND SHOULD BE USED WHEN YOU KNOW WHAT IS THIS ALL ABOUT --

  Sessions memcached / SQL class. Session libraries are not static because of php 5.3 behaviour in destructing classes.
  For table structure look in extend/db_scripts/table_sessions.sql
*/

class session
{
	// -- Variable for cookie information, session id is stored in $cookie['value']
  public $cookie = NULL;
	public $is_open = FALSE;


	// -- Constructor
  public function __construct($name = 'S', $expire = array('cookie' => 1800, 'session' => 604800), $path = '/', $domain = NULL, $https = false, $http_only = false)
  {
    $this->cookie = array(
      'name' => $name,
      'expire' => $expire,
      'path' => $path,
      'domain' => $domain,
      'https' => $https,
      'http_only' => $http_only,
    );
  }


	// -- Destructor
  public function __destruct()
  {
		$this->close();
  }


	// -- Make a hash from an specified id 
  public function hash($id)
  {
    return sha1($id . 'SomeUnknownStringToMakeItHarderToGuess');
  }


	// -- Open new session
	public function open($id = NULL)
	{
    // Get the id OR set a new one    
		switch (true)
		{
			case (!empty($id)):
				$this->set_id($id, false);
			break;

      case (!empty($this->cookie['value'])):
        $this->set_id($this->cookie['value'], false);
      break;

			case (isset($_COOKIE[$this->cookie['name']]) && strlen($_COOKIE[$this->cookie['name']]) == 40):
				$this->set_id($_COOKIE[$this->cookie['name']], false);
			break;

			default:
				$this->set_id();
			break;
		}

		// Read the session data
		$_SESSION = $this->read($this->cookie['value']);

		// Check for an ip address
		if (!empty($_SESSION['IP']) && ($_SESSION['IP'] != $_SERVER['REMOTE_ADDR'] || $_SESSION['USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']))
		{
		  $_SESSION = array();
			$this->set_id();
		}

    // Session is open
		$this->is_open = TRUE;
	}


	// -- Close session
  public function close($save = true, $delete = false)
  {
		if (!empty($this->is_open) && isset($_SESSION))
		{
			// Save session
			if (!empty($save))
			{
        $_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
				$this->write($this->cookie['value'], $_SESSION);
			}

			// Delete session
			if (!empty($delete))
			{
				g()->memcache->delete($this->cookie['name'] . $this->hash($this->cookie['value']));
			}

			// Close it by unsetting $_SESSION variable
			$this->is_open = FALSE;
			unset($_SESSION);
		}
  }


	// -- Set session id
  public function set_id($id = NULL, $rehash = true)
  {
    // If empty, generate a new id
    if (empty($id))
    {
      $id = time() . rand(-999999, 999999999999);
    }

    // Set new session id and send a cookie to the browser
		$this->cookie['value'] = (empty($rehash) ? $id : $this->hash($id));
    setcookie($this->cookie['name'], $this->cookie['value'], time() + $this->cookie['expire']['cookie'], $this->cookie['path'], $this->cookie['domain'], $this->cookie['https'], $this->cookie['http_only']);

    // Return new session id
    return $this->cookie['value'];
  }


	// -- Read session data
  public function read($id)
  {
    $data = g()->memcache->get($this->cookie['name'] . $this->hash($id));
    return (empty($data) ? array() : $data);

    // Fallback
  	/*
		if ($data === false)
  	{
    	$res = db::fetch("SELECT data FROM sessions WHERE id = ?", $id);
  		if (!empty($res->data))
  		{
  			$data = $res->data;
  		}
  	}
    return $data;*/
  }


	// -- Write session data
  public function write($id, $data)
  {
    g()->memcache->set($this->cookie['name'] . $this->hash($id), $data, NULL, $this->cookie['expire']['session']);
    return true;

    // Fallback
		/*
    $res = db::fetch('
      UPDATE sessions 
      SET 
        id = '. db::db_link()->quote($id) .',
        uid = '. (empty($_SESSION['user']->uid) ? 'NULL' : $_SESSION['user']->uid) .',
        expires = '. db::db_link()->quote(time()) .',
        data = '. db::db_link()->quote($data) .'
      WHERE id = '. db::db_link()->quote($id) .' 
      RETURNING id
    ');

    // Due to some php bug, have to build php from svn trunk or snapshot?
    try
    {
    if (empty($res->id))
    {
      db::query(
        "INSERT INTO sessions VALUES(?, ". (empty($_SESSION['user']->uid) ? 'NULL' : $_SESSION['user']->uid) .", ?, ?);", 
        array($id, time(), $data)
      );  	
  	}
  	}
  	catch(Exception $e)
  	{
  	 //$res = db::fetch('SELECT count(id) as count from sessions WHERE id = ?', $id);
  	 //error_log(print_r($res, true));
  	 //error_log($_SERVER['REMOTE_ADDR']);
  	 //error_log($id);
  	 //error_log($e->getMessage());
  	 //error_log(print_r($res, true));
  	}

    return true;*/
  }







	// -- Garbage collector, not supported for memcache
  public function gc($max)
  {
    db::query("DELETE FROM sessions WHERE expires <= ?", (time() - $max));
  
    // Cleanup user other sessions
    if (!empty($_SESSION['user']->uid) && empty($_SESSION['fake']))
    {
      db::query("DELETE FROM sessions WHERE uid = ? AND id != ?", array($_SESSION['user']->uid, session_id()));
    }
    return true;
  }
}

?>