<?php

/*
|--------------------------------------------------------------------------
| PDO backed up session class
|
| For table structure look for table_sessions_*.sql file.
|--------------------------------------------------------------------------
*/

namespace System\Modules\Utils\Models\Sessions;

class Sessions implements \SessionHandlerInterface
{
    protected ?Sessions $backupHandler = null;

    protected int $expire = 0;
    protected string $sessionName = '';
    protected string $salt = '';

    public function __construct($sessionName = 'S', ?Sessions $backupHandler = null)
    {
        ini_set('session.use_only_cookies', true);
        ini_set('session.entropy_file', '/dev/urandom');

        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 100);

        ini_set('session.gc_maxlifetime', 432000 * 4);
        ini_set('session.cookie_lifetime', 432000 * 4);

        ini_set('session.hash_function', 'sha512');
        ini_set('session.hash_bits_per_character', 5);

        $this->sessionName = $sessionName;
        $this->backupHandler = $backupHandler;
        $this->salt = md5($_SERVER['HTTP_USER_AGENT']);
        $this->expire = session_cache_expire() * 60;
    }

    public function register(): void
    {
        session_name($this->sessionName);
        session_set_save_handler($this, true);
    }

    public function start(): void
    {
        session_start();
    }

    public function id(string $id)
    {
        return "{$this->sessionName}_{$id}";
    }

    public function open(string $path, string $name): bool
    {
        if (!empty($this->backupHandler)) {
            return $this->backupHandler->open($path, $name);
        }
        return true;
    }

    public function close(): bool
    {
        if (!empty($this->backupHandler)) {
            return $this->backupHandler->close();
        }

        return true;
    }

    public function read(string $id): string|false
    {
        if (!empty($this->backupHandler)) {
            return $this->backupHandler->read($id);
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        if (!empty($this->backupHandler)) {
            return $this->backupHandler->write($id, $data);
        }

        return true;
    }

    public function destroy(string $id): bool
    {
        // Delete cookie, if possible
        if (headers_sent() == false) {
            setcookie($this->sessionName, '', time() - 1, '/');
        }

        if (!empty($this->backupHandler)) {
            return $this->backupHandler->destroy($id);
        }

        return true;
    }

    public function gc(int $maxLifetime): int|false
    {
        if (!empty($this->backupHandler)) {
            return $this->backupHandler->gc($maxLifetime);
        }

        return false;
    }
}
