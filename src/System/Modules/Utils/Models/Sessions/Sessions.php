<?php

/*
|--------------------------------------------------------------------------
| PDO backed up session class
|
| For table structure look for table_sessions_*.sql file.
|--------------------------------------------------------------------------
*/

namespace System\Modules\Utils\Models\Sessions;

use System\Modules\Core\Exceptions\ErrorMessage;

class Sessions implements \SessionHandlerInterface
{
    protected $expire = null;
    protected $sessionName = null;
    protected $salt = null;
    protected $data = [];

    public function __construct($sessionName = 'S')
    {
        $this->sessionName = $sessionName;
        $this->salt = md5($_SERVER['HTTP_USER_AGENT']);

        session_name($this->sessionName);
        ini_set('session.use_only_cookies', true);

        ini_set('session.entropy_file', '/dev/urandom');

        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 100);

        ini_set('session.gc_maxlifetime', 432000*4);
        ini_set('session.cookie_lifetime', 432000*4);

        ini_set('session.hash_function', 'sha512');
        ini_set('session.hash_bits_per_character', 5);

        // Set some variables
        $this->expire = session_cache_expire() * 60;

        // Register session handler
        session_set_save_handler($this, true);
    }

    public function start()
    {
        session_start();
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        throw new ErrorMessage('Not implemented', 10101);
    }

    public function write(string $id, string $data): bool
    {
        throw new ErrorMessage('Not implemented', 10102);
    }

    public function destroy(string $id): bool
    {
        // Delete cookie, if possible
        if (headers_sent() == false) {
            setcookie($this->sessionName, '', time() - 1, '/');
        }

        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        throw new ErrorMessage('Not implemented', 10103);
    }
}
