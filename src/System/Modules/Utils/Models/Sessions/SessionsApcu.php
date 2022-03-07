<?php

/*
|--------------------------------------------------------------------------
| Apc session class
|
| Extends sessions class as an optional backup
|--------------------------------------------------------------------------
*/

namespace System\Modules\Utils\Models\Sessions;

class SessionsApcu extends Sessions
{
    public function __construct($sessionName = 'SAPC', ?Sessions $backupHandler = null)
    {
        parent::__construct($sessionName, $backupHandler);
    }

    public function read(string $id): string|false
    {
        $data = apcu_fetch($this->id($id));
        if (!empty($data)) {
            return $data;
        }

        return parent::read($id);
    }

    public function write(string $id, string $data): bool
    {
        apcu_store($this->id($id), $data, $this->expire);
        if (!empty($this->db_link)) {
            parent::write($id, $data);
        }

        return parent::write($id, $data);
    }

    public function destroy(string $id): bool
    {
        apcu_delete($this->id($id));
        if (!empty($this->db_link)) {
            parent::destroy($id);
        }

        return parent::destroy($id);
    }
}
