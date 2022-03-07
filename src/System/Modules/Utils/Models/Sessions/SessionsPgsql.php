<?php

/*
|--------------------------------------------------------------------------
| PDO backed up session class
|
| For table structure look for table_sessions_*.sql file.
|--------------------------------------------------------------------------
*/

namespace System\Modules\Utils\Models\Sessions;

use System\Modules\Utils\Models\Db;

class SessionsPgsql extends Sessions
{
    public string $dbConfigName = 'sessions';
    private $db_link = null;

    public function __construct(array $dbConfig, $sessionName = 'SMC', ?Sessions $backupHandler = null)
    {
        Db::init($this->dbConfigName, $dbConfig);

        parent::__construct($sessionName, $backupHandler);
    }

    public function read(string $id): string|false
    {
        $res = Db::fetch(
            'SELECT data FROM sessions WHERE id = ? AND salt = ?',
            [$id, $this->salt],
            $this->dbConfigName
        );
        if (!empty($res->data)) {
            return $res->data;
        }

        return parent::read($id);
    }

    public function write(string $id, string $data): bool
    {
        $statement = Db::query(
            '
                UPDATE sessions
                SET data = ?, timestamp = CURRENT_TIMESTAMP
                WHERE id = ? AND salt = ?
            ',
            [$data, $id, $this->salt],
            $this->dbConfigName
        );

        if ($statement->rowCount() == 0) {
            Db::query(
                'INSERT INTO sessions (id, salt, data) VALUES (?, ?, ?)',
                [$id, $this->salt, $data],
                $this->dbConfigName
            );
        }

        return parent::write($id, $data);
    }

    public function destroy(string $id): bool
    {
        Db::query(
            'DELETE FROM "sessions" WHERE "id" = ?',
            [$id],
            $this->dbConfigName
        );

        return parent::destroy($id);
    }

    public function gc(int $maxLifetime): int|false
    {
        Db::query(
            "DELETE FROM sessions WHERE timestamp <= CURRENT_TIMESTAMP - INTERVAL '{$maxLifetime}' SECOND",
            name: $this->dbConfigName
        );

        return parent::gc($maxLifetime);
    }
}
