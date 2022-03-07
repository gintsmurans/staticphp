<?php

namespace System\Modules\Utils\Models\Cache;

/**
 *  Apcu cache implementation
 */
class CacheApcu extends Cache
{
    /**
     *  Set cached value by key using APC.
     *
     * @access public
     * @static
     * @return bool
     */
    public function setValue(string $key, mixed $value, ?int $ttl = null): bool
    {
        return apcu_store($this->prefix($key), $value, $ttl === null ? 0 : $ttl);
    }

    /**
     *  Get cached value by key using APC.
     *
     * @access public
     * @static
     * @return mixed
     */
    public function getValue(string $key): mixed
    {
        return apcu_fetch($this->prefix($key));
    }

    /**
     *  Get cached value by key using APC.
     *
     * @access public
     * @static
     * @return bool
     */
    public function removeKey(string $key): bool
    {
        return apcu_delete($this->prefix($key));
    }
}
