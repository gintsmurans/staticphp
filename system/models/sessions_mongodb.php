<?php

/*
|--------------------------------------------------------------------------
| MongoDB session handler
|--------------------------------------------------------------------------
*/

namespace models;

class sessions_mongodb
{
  private $st = NULL; // session table
  private $data = NULL;
  private $salt = NULL;


  public function __construct(&$mdb)
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
    $this->st = $mdb->sessions;
    $this->salt = md5($_SERVER['HTTP_USER_AGENT']);

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

  public function open()
  {
    // Do nothing
  }

  public function close()
  {
    // Do nothing
  }


  public function read($id)
  {
    $this->data = $this->st->find(array('id' => $id, 'check' => $this->salt))->fields(array('data' => TRUE))->getNext();
    return (empty($this->data) ? NULL : $this->data['data']);
  }


  public function write($id, $data)
  {
    $this->data['id'] = $id;
    $this->data['data'] = $data;
    $this->data['check'] = $this->salt;
    $this->data['expires'] = time();
    $this->st->save($this->data);

    return TRUE;
  }


  public function destroy($id)
  {
    $this->st->remove(array('id' => $id));

    // Also delete the cookie
    if (headers_sent() == FALSE)
    {
      setcookie($this->prefix, '', time() - 1, '/'); 
    }

    return TRUE;
  }


  public function gc($max)
  {
    $this->st->remove(array('expires' => array('$lt' => (time() - $max))));
    return TRUE;
  }
}

?>