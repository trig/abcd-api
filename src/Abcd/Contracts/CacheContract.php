<?php

namespace Abcd\Contracts;

interface CacheContract
{
    /**
     * @param string $id Cache identifier
     * @param \Serializable $data
     * @param int $ttl
     */
    public function put(string $id, $data, int $ttl = 0): void;

    /**
     * @param string $id Cache identifier
     * @return mixed|null NULL will be returned in case of cache miss
     */
    public function get($id);

    /**
     * @param string $id Cache identifier
     */
    public function delete($id): void;

    /**
     * @param string $id Cache identifier
     * @return bool
     */
    public function has($id): bool;

    /**
     * Updates only value, not TTL
     * @param string $id Cache identifier
     * @return int|null Incremented value
     */
    public function updateCounter($id, int $value): ?int;

}