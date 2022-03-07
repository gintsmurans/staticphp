<?php

/*
|--------------------------------------------------------------------------
| MongoDB session handler
|--------------------------------------------------------------------------
*/

namespace System\Modules\Utils\Models\Sessions;

class SessionsMongoDb extends Sessions
{
    protected $mdbConnectionString = null;
    protected $mdbConnection = null;
    protected $mdbDatabase = null;
    protected $mdbCollection = null;

    public function __construct($connectionString, $sessionName = 'SMDB')
    {
        $this->mdbConnectionString = $connectionString;
        $this->mdbConnection = new \MongoDB\Client($this->mdbConnectionString);
        $this->mdbDatabase = $this->mdbConnection->sessions;
        $this->mdbCollection = $this->mdbDatabase->php_sessions;

        parent::__construct();
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
        $this->data = $this->mdbCollection
            ->find(['id' => $id, 'check' => $this->salt])
            ->fields(['data' => true])
            ->getNext();

        return (empty($this->data) ? null : $this->data['data']);
    }

    public function write(string $id, string $data): bool
    {
        $this->data['id'] = $id;
        $this->data['data'] = $data;
        $this->data['check'] = $this->salt;
        $this->data['expires'] = time();
        $this->mdbCollection->save($this->data);

        return true;
    }

    public function destroy(string $id): bool
    {
        $this->mdbCollection->remove(['id' => $id]);
        // Also delete the cookie
        if (headers_sent() == false) {
            setcookie($this->prefix, '', time() - 1, '/');
        }

        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $this->mdbCollection->remove(['expires' => ['$lt' => (time() - $max_lifetime)]]);

        return true;
    }
}
