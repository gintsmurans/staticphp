<?php 

class filesystem
{
	// How many directory levels create
	public static $levels = array(2 /* Levels deep */, 2 /* Directory name length */);


	// Return random hash
	public static function random_hash()
	{
		return sha1(time() . rand(1, 9999));
	}


	// Return full path to a file
	public static function hashed_path($filename, $randomize = FALSE, $create_directories = FALSE)
	{	
		// Explode path to get filename
		$parts = explode(DIRECTORY_SEPARATOR, $filename);

		// Predefine array elements
		$data['hash_dir'] = '';
		$data['hash_file'] = '';

		// Get filename and extension
		$data['filename'] = explode('.', array_pop($parts));
		$data['ext'] = (count($data['filename']) > 1 ? array_pop($data['filename']) : '');
		$data['filename'] = (empty($randomize) ? implode('.', $data['filename']) : self::random_hash());

		// Put directory together
		$dir = (empty($parts) ? '' : implode('/', $parts) . '/');

		// Create hashed directory
		for ($i = 1; $i <= self::$levels[0]; ++$i)
		{
			$data['hash_dir'] .= substr($data['filename'], -1 * self::$levels[1] * $i, self::$levels[1]) . '/';
		}

		// Put other stuff together
		$data['dir'] = str_replace($data['hash_dir'], '', $dir) . $data['hash_dir'];
		$data['file'] = $data['dir'] . $data['filename'] . (empty($data['ext']) ? '' : '.' . $data['ext']);
		$data['hash_file'] = $data['hash_dir'] . $data['filename'] . (empty($data['ext']) ? '' : '.' . $data['ext']);

		// Create directories
		if (!empty($create_directories) && !is_dir($data['dir']))
		{
			mkdir($data['dir'], 0777, true);
		}

		return $data;
	}
	
	
	public static function hashed_delete($filename)
	{
		$path = self::hashed_path($filename);

		// Trim off / from end
		$path['hash_dir'] = rtrim($path['hash_dir'], '/');
		$path['dir'] = rtrim($path['dir'], '/');

		// Explode hash directories to get the count of them
		$expl = explode('/', $path['hash_dir']);
		
		// Unlink the file
		if (is_file($path['file']))
		{
			unlink($path['file']);
		}

		// Remove directories
		foreach ($expl as $null)
		{
			if (!@rmdir($path['dir']))
			{
				break;
			}

			$path['dir'] = dirname($path['dir']);
		}
	}
}

?>