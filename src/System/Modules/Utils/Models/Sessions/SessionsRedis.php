<?php

/*
|--------------------------------------------------------------------------
| Redis session handler
|--------------------------------------------------------------------------
*/

namespace System\Modules\Utils\Models\Sessions;

use Redis;

class SessionsRedis extends Sessions
{
    protected $redis = null;

    public function __construct($dbConfig, $sessionName = 'SMDB', ?Sessions $backupHandler = null)
    {
        $this->redis = new Redis();
        $this->redis->connect($dbConfig['hostname'], $dbConfig['port']);
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
        $this->redis->select(isset($dbConfig['database']) ? $dbConfig['database'] : 1);

        parent::__construct($sessionName, $backupHandler);
    }

    public function read(string $id): string|false
    {
        $data = $this->redis->get($this->id($id));
        if (!empty($data)) {
            return $data;
        }

        return parent::read($id);
    }

    public function write(string $id, string $data): bool
    {
        $this->redis->set($this->id($id), $data, $this->expire);

        return parent::write($id, $data);
    }

    public function destroy(string $id): bool
    {
        $this->redis->del($this->id($id));

        return parent::destroy($id);
    }
}
