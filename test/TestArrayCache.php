<?php

namespace DDesrosiers\Test\SilexAnnotations;

use Psr\SimpleCache\CacheInterface;

class TestArrayCache implements CacheInterface
{
    protected $fetchedIDs;
    private $cache;

    public function wasFetched($id)
    {
        return isset($this->fetchedIDs[$id]);
    }

    public function get($id, $default = null)
    {
        $this->fetchedIDs[$id] = true;
        return $this->cache[$id] ?? $default;
    }

    public function clearWasFetched()
    {
        $this->fetchedIDs = [];
    }

    public function set($key, $value, $ttl = null)
    {
        $this->cache[$key] = $value;
    }

    public function delete($key)
    {
        unset($this->cache[$key]);
    }

    public function clear()
    {
        $this->cache = [];
    }

    public function getMultiple($keys, $default = null)
    {
        // TODO: Implement getMultiple() method.
    }

    public function setMultiple($values, $ttl = null)
    {
        // TODO: Implement setMultiple() method.
    }

    public function deleteMultiple($keys)
    {
        // TODO: Implement deleteMultiple() method.
    }

    public function has($key)
    {
        return isset($this->cache[$key]);
    }
}
