<?php

//
// Draugiem.lv API Client
//
// Copyright 2009 Gints Murans. All Rights Reserved.
//

namespace models;

class draugiem
{

	// Public variables
	public $api_id = null;
	public $api_key = null;
	public $user_key = null;
	public $call_count = 0;


	// Private variables
	private $api_domain = 'http://api.draugiem.lv/json/';
	private $auth_url = 'https://api.draugiem.lv/authorize/';


	// Attached files, clears after each call
	private $file = null;


	/**
	* Init php client
	*
	* @param string $api_key your api_key
	* @param string $user_key session key returned by user auth
	*/
	public function __construct($api_id, $api_key, $user_key = null)
	{
    $this->api_id = $api_id;
		$this->api_key = $api_key;
		$this->user_key = $user_key;
	}


	/*
		Returns URL to Draugiem.lv Passport authorization page to authenticate user
		$redirect_url = URL where the user has to be redirected after the authorization process.
		The URL must be within the same domain that is written in properties of the application.
	*/
	public function get_login_url($redirect_url)
	{
		$hash = md5($this->api_key.$redirect_url);
		return $this->auth_url.'?app='.$this->api_id.'&hash='.$hash.'&redirect='.urlencode($redirect_url);
	}


	/**
	* Call user method from server
	*
	* @param string $name fills automatically, for example when calling $gsoc->func_name as func_name
	* @param mixed $args contains arguments from method called
	* @return object simplexml object or boolean false
	*/
	public function __call($name, $args)
	{
		// Define some variables
		$return = '';
		$args = (!empty($args[0]) ? $args[0] : null);

    // Increase call count
    ++$this->call_count;

    // Add action
    $args['action'] = $name;

    // Add api_key
		$args['app'] = $this->api_key;

		// Add session key
		if (!empty($this->user_key))
		{
      $args['apikey'] = $this->user_key;
    }

    // Build request data
    // $opts = $this->_build_multipart($args);
    $opts = $this->_build_simple($args);

		// Create context and open connection to server
		$context = stream_context_create($opts);

		try
		{
		  // Get remote file
		  $return = file_get_contents($this->api_domain, false, $context);

			// Set file to null
			$this->file = null;

      // Return data
			return json_decode($return);
    }
    catch(Exception $e)
    {
      return false;
    }
	}


  /*
  * Create simple request array
  *
  * @param array $args Array of data to send to the api server
  * @return array stream_context_create array
  */
	private function _build_simple($args)
	{
    $data = http_build_query($args);
    $opts = array(
      'http'=>array(
        'method' => 'POST',
        'user_agent' => 'Draugiem API Simple Request, v2.0',
        'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" .
                    'Content-Length: ' . strlen($data) . "\r\n",
        'content' => $data
      )
    );
    return $opts;
	}


  /*
  * Create multipart/form-data request array
  *
  * @param array $args Array of data to send to the api server
  * @return array stream_context_create array
  */
	private function _build_multipart($args)
	{
    $b = 'dr-boundary';
    $data = '';

    // Add args to $data, curently supports only two-dimensional arrays
    if (!empty($args))
    {
      foreach ($args as $key=>$value)
      {
        if (is_array($value))
        {
          foreach ($value as $skey=>$svalue)
          {
            $data .= "--{$b}\r\n";
            $data .= "Content-Disposition: form-data; name=\"{$key}[{$skey}]\"\r\n\r\n";
            $data .= "$svalue\r\n";
          }
        }
        else
        {
          $data .= "--{$b}\r\n";
          $data .= "Content-Disposition: form-data; name=\"$key\"\r\n\r\n";
          $data .= "$value\r\n";
        }
      }
    }

    // Add files to $data
    if (!empty($this->file) && is_file($this->file))
    {
      $data .= "--{$b}\r\n";
      $data .= "Content-Disposition: file; name=\"file\"; filename=\"{$this->file}\"\r\n";
      $data .= "Content-Type: application/octet-stream\r\n";
      $data .= "Content-Transfer-Encoding: binary\r\n\r\n";
      $data .= file_get_contents($this->file)."\r\n";
    }

    $data .= "--{$b}--";

    // Create request array
    $opts = array(
      'http'=>array(
        'method' => 'POST',
        'user_agent' => 'Draugiem API Multipart Request, v2.0',
        'header' => 'Content-type: multipart/form-data, boundary=' . $b . "\r\n" .
        'Content-Length: ' . strlen($data) . "\r\n",
        'content' => $data
      )
    );
    return $opts;
	}


	/**
	*
	* Attach file
	*
  * @param string $file Path to file
  * @return null
	*/
	public function attach($file)
	{
    if (!empty($file))
    {
      $this->file = (string) $file;
    }
	}
}

?>