<?php

/*
|--------------------------------------------------------------------------
| Memcached session class
|
| Extends sessions class as an optional backup
|--------------------------------------------------------------------------
*/

namespace System\Modules\Utils\Models\Sessions;

use Memcached;

class SessionsMemcached extends Sessions
{
    private $memcached = null;

    /**
     * @var $servers List of servers. Example: [[127.0.0.1, 112211], [192.168.1.10, 112211]]
     */
    public function __construct(
        array $servers,
        ?string $persistentId = null,
        $sessionName = 'SMC',
        ?Sessions $backupHandler = null
    ) {
        $this->memcached = new Memcached($persistentId);
        $this->memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        if (!count($this->memcached->getServerList())) {
            $this->memcached->addServers($servers);
        }

        parent::__construct($sessionName, $backupHandler);
    }

    public function read(string $id): string|false
    {
        $data = $this->memcached->get($this->id($id));
        if (!empty($data)) {
            return $data;
        }

        return parent::read($id);
    }

    public function write(string $id, string $data): bool
    {
        $this->memcached->set($this->id($id), $data, $this->expire);

        return parent::write($id, $data);
    }

    public function destroy(string $id): bool
    {
        $this->memcached->delete($this->id($id));

        return parent::destroy($id);
    }
}
