<?php

//
// CCarrot client
//
// Copyright 2009 Gints Murāns. All Rights Reserved.
//


class ccarrot
{

	// Public variables
	public $api_key = null;
	public $session_key = null;
	
	
	// Private variables
	private $api_domain = 'api.ccarrot.com';


	/** 
	* Init php client
	*
	* @param string $api_key your api_key
	* @param string $session_key session key returned by user auth
	*/ 
	public function __construct($api_key, $session_key = null)
	{
		$this->api_key = $api_key;
		$this->session_key = $session_key;
	}



	/**
	* Call user method from server
	*
	* @param string $name fills automatically, for example when calling $gsoc->func_name as func_name
	* @param mixed $args contains arguments from method called
	* @return mixed json object or boolean false
	*/
	public function __call($name, $args)
	{
	
		// Define some variables
		$data = '';
		$return = '';
		$error = array();

		// Add session key
		if (!empty($this->session_key))
		{
			$args[0]['session_key'] = $this->session_key;
		}

		// IF not empty $args, build http query
		if (!empty($args[0]))
		{
			$data = http_build_query($args[0]);
		}
		
		
		// Create request array
		$opts = array(
		  'http'=>array(
		    'method'=>"POST",
		    'header'=>"Content-type: application/x-www-form-urlencoded\r\n" .
		              "Content-Length: " . strlen($data) . "\r\n",
            'content' => $data
		  )
		);
		
		
		// Create context and open connection to server
		$context = stream_context_create($opts);
		$open = fopen('http://'.$this->api_domain.'/simple/'.$this->api_key.'/'.$name, 'r', false, $context);
		
		
		// If conection successfull
		if ($open)
		{
			// Get return data
			while (!feof($open))
			{
				$return .= fgets($open, 2048);
			}
			fclose($open);
			
			// If magic quotes on, strip slashes to avoid json_decode to fail
			if (get_magic_quotes_gpc())
			{
				$return = stripslashes($return);
			}

			return json_decode($return);
		}
	}
}

?>