<?php

/*
|--------------------------------------------------------------------------
| PDO backed up session class
|
| For table structure look for table_sessions_*.sql file.
|--------------------------------------------------------------------------
*/

namespace models;

class sessions
{
  public $prefix = NULL;
  public $expire = NULL;

  private $db_link = NULL;

  public function __construct(&$db_link)
  {  
    // Secure our sessions a little bit more
    session_name('SSSSS');
    ini_set('session.use_only_cookies', TRUE);

    ini_set('session.entropy_file', '/dev/urandom');

    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
    
    ini_set('session.gc_maxlifetime', 432000*4);
    ini_set('session.cookie_lifetime', 432000*4);
    
    ini_set('session.hash_function', 'sha512');
    ini_set('session.hash_bits_per_character', 5);


    // Set some variables
    $this->db_link = $db_link;
    $this->prefix = session_name();
    $this->expire = session_cache_expire() * 60;


    // Register session handler
    session_set_save_handler(
      array($this, 'open'),
      array($this, 'close'),
      array($this, 'read'),
      array($this, 'write'),
      array($this, 'destroy'),
      array($this, 'gc')
    );
    session_start();
  }

  public function __destruct() 
  { 
    session_write_close(); 
  } 

  public function open()
  {
    return TRUE;
  }


  public function close()
  {
    return TRUE;
  }


  public function read($id)
  {
   $res = self::query('SELECT "data" FROM "sessions" WHERE "id" = ?', $id)->fetch();
  if (!empty($res->data))
  {
   return $res->data;
  }
    return NULL;
  }


  public function write($id, $data)
  {
    self::destroy($id);
    self::query('INSERT INTO sessions VALUES (?, ?, ?)', array($id, $data, time()));
    return TRUE;
  }


  public function destroy($id)
  {
    self::query('DELETE FROM "sessions" WHERE "id" = ?', $id);

    // Also delete the cookie
    if (headers_sent() == FALSE)
    {
      setcookie($this->prefix, '', time() - 1, '/'); 
    }

    return TRUE;
  }


  public function gc($max)
  {
    db::query('DELETE FROM "sessions" WHERE "expires" <= ?', (time() - $max));
    return TRUE;
  }


  private function query($query, $data = NULL, $name = 'default')
  {
    if (empty($query))
    {
      return NULL;
    }

    if (empty($this->db_link))
    {
      throw new \Exception('No connection to database');
    }

    // Do request
    $prepare = $this->db_link->prepare($query);
    $prepare->execute((array)$data);

    return $prepare;
  }
}

?>