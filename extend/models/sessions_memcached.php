<?php

/*
  Memcached sessions, extends sessions class as backup. Session classes are not static because of php 5.3 behaviour in destructing classes.
*/

namespace models;

class sessions_memcached extends sessions
{
  private $memcached = NULL;
  private $avoid_db = FALSE;


  public function __construct(&$memcached, $avoid_db = FALSE)
  {
    $this->memcached = $memcached;
    $this->avoid_db = $avoid_db;
    parent::__construct();
  }


  public function read($id)
  {
    $data = $this->memcached->get($this->prefix . $id);
    if (!empty($data))
    {
      return $data;
    }

    return (empty($this->avoid_db) ? parent::read($id) : NULL);
  }


  public function write($id, $data)
  {
    $this->memcached->set($this->prefix . $id, $data, $this->expire);

    if (empty($this->avoid_db))
    {
      parent::write($id, $data);
    }

    return TRUE;
  }


  public function destroy($id)
  {
    $this->memcached->delete($this->prefix . $id);

    if (empty($this->avoid_db))
    {
      parent::destroy($id);
    }

    return TRUE;
  }
}

?>