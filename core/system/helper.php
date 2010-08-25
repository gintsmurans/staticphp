<?php

// -- Autoload classes, currently only support for db is available (because of not-to-have-a-messy-code)
/*function __autoload($class_name)
{
	global $config;
  if ($class_name === 'db' && !empty($config->db[$config->db['autoload']]))
  {
    include_once SYS_PATH . 'db.php';
    db::init($config->db['autoload'], $config->db[$config->db['autoload']], $config->debug);
  }
}*/

?>