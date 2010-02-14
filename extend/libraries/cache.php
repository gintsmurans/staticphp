<?php

/*
  "StaticPHP Framework" - Simple PHP Framework
  
  ---------------------------------------------------------------------------------
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  ---------------------------------------------------------------------------------
  
  Copyright (C) 2009  Gints Murāns <gm@gm.lv>
*/


class cache
{
  public $cache = NULL;
  private $delete_delayed = NULL;

  public function __construct(&$cache)
  {
    // Choose caching configuration  
    if (count($cache) > 1)
    {
      foreach ($cache as $key => &$value)
      {
        if (preg_match('/'. preg_quote($key, '/') .'/', $_SERVER['REQUEST_URI']))
        {
          $this->cache =& $value;
        }
      }
    }
    else
    {
      $this->cache =& $cache[''];
    }
    
    // Set time
    $this->cache['time'] = time();

    // Check for allowed method    
    if (!in_array(strtolower($_SERVER['REQUEST_METHOD']), $this->cache['methods']))
    {
      $this->cache['cached'] = true;
      return;
    }

    // Create hash from provided key
    $this->cache['hash'] = $this->make_hash($this->cache['key']);
    $this->cache['full_hash'] = $this->make_full_hash($this->cache['hash']);

    // Load hash table
    $this->cache['hash_table'] = (array) apc_fetch('staticphp_cache_table');

    // Call appropriate method for reading cache
    $this->{$this->cache['type'] . '_read'}();

    // Start buffering if not already cached
    if (empty($this->cache['cached']))
    {
      ob_start();
    }
    else
    {
      exit;
    }
  }


  public function __destruct()
  {
    // If not cached, do it
    if (empty($this->cache['cached']))
    {
      $this->cache['hash_table'][$this->cache['full_hash']] = $this->cache['time'];
      $this->cache['contents'] = ob_get_contents();
      $this->{$this->cache['type'] . '_save'}();
    }

    // Delete delayed
    if (!empty($this->delete_delayed))
    {
      foreach ($this->delete_delayed as $key => &$value)
      {
        $this->delete_now($key, $value);
      }
    }

    // Garbage collector
    if (empty($this->cache['hash_table']['last_check']))
    {
      $this->cache['hash_table']['last_check'] = $this->cache['time'];
    }
    elseif ($this->cache['time'] > $this->cache['hash_table']['last_check'] + $this->cache['timeout'])
    {
      foreach ($this->cache['hash_table'] as $key => &$value)
      {
        if ($this->cache['time'] > $value + $this->cache['timeout'])
        {
          $this->delete_now($this->cache['hash_table'][$key]);
          unset($this->cache['hash_table'][$key]);
        }
      }
    }

    // Store hash table into memory
    apc_store('staticphp_cache_table', new ArrayObject($this->cache['hash_table']));
  }


  public function delete($key = NULL, $regexp = false)
  {
    $this->delete_delayed[$key] = $regexp;
  }


  public function delete_now($key = NULL, $regexp = false)
  {
    if (!empty($this->cache['type']))
    {
      $this->{$this->cache['type'] . '_delete'}($key, $regexp);
    }
  }


  /* ================================ HASH ================================ */
  private function make_hash($key)
  {
    return md5($key);
  }

  private function make_full_hash($hash)
  {
    return $this->cache['prefix'] . $hash[32 - 1] . '/' . substr($hash, -3, 2) . '/' . $hash;
  }

  private function search_hash_table($key)
  {
    $keys = NULL;
    if (!empty($key))
    {
      foreach ($this->cache['hash_table'] as $key1 => $null)
      {
      	
        if (preg_match('/'. preg_quote($key, '/') .'/', $key1))
        {
          $keys[] = $key1;
        }
      }
    }
    return $keys;
  }
  /* ================================ !HASH ================================ */



  /* ================================ APC =================================== */
  private function apc_read()
  {
    $data = apc_fetch($this->cache['full_hash']);
    if (!empty($data))
    {
      $this->cache['cached'] = true;
      echo $data;
    }
  }

  private function apc_save()
  {
    apc_store($this->cache['full_hash'], $this->cache['contents'], $this->cache['timeout']);
  }

  private function apc_delete($key = NULL, $regexp = false)
  {
    switch(true)
    {
      case empty($key):
        $key = $this->cache['full_hash'];
      break;
      
      case !empty($regexp):
        $key = $this->search_hash_table($key);
      break;
    }
    
    foreach ((array)$key as $item)
    {
      apc_delete($item);
    }    
  }
  /* ================================ !APC =================================== */
  


  /* ================================ FILES ================================ */
  private function files_read()
  {
    if (substr($this->cache['files_tmp_path'], -1, 1) !== '/')
    {
      $this->cache['files_tmp_path'] .= '/';
    }

    $this->cache['directory'] = dirname($this->cache['files_tmp_path'] . $this->cache['full_hash']) . '/';
    $this->cache['file'] = $this->cache['directory'] . $this->cache['hash'];
    
    if (is_file($this->cache['file']))
    {
      if (!empty($this->cache['hash_table'][$this->cache['full_hash']]) && $this->cache['time'] > $this->cache['hash_table'][$this->cache['full_hash']] + $this->cache['timeout'])
      {
        unlink($this->cache['file']);
      }
      else
      {
        $this->cache['cached'] = true;
        echo file_get_contents($this->cache['file']);
      }
    }
  }

  private function files_save()
  {
    if (!is_dir($this->cache['directory']))
    {
      mkdir($this->cache['directory'], 0777, true);
    }
    file_put_contents($this->cache['file'], $this->cache['contents']);
  }
  
  private function files_delete($key = NULL, $regexp = false)
  {
    switch(true)
    {
      case empty($key):
        $key = $this->cache['full_hash'];
      break;
      
      case !empty($regexp):
        $key = $this->search_hash_table($key);
      break;
    }

    foreach ((array)$key as $item)
    {
      if (is_file($this->cache['files_tmp_path'] . $item))
      {
        unlink($this->cache['files_tmp_path'] . $item);
      }
    }
  }
  /* ================================ !FILES ================================ */
}

?>