<?php

namespace DDesrosiers\SilexAnnotations\Cache;

use Psr\SimpleCache\CacheInterface;

class MemoCache implements CacheInterface
{
    private $cache = [];
    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        $this->isValidKey($key);

        return  array_key_exists($key, $this->cache) ? $this->cache[$key]: $default;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null)
    {
        $this->isValidKey($key);
        $this->cache[$key] = $value;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->isValidKey($key);
        unset($this->cache[$key]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->cache = [];
    }

    /**
     * @inheritdoc
     */
    public function getMultiple($keys, $default = null)
    {
        $this->isValidKeys($keys);
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    /**
     * @inheritdoc
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->isValidKeys($values);
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteMultiple($keys)
    {
        $this->isValidKeys($keys);
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        $this->isValidKey($key);

        return array_key_exists($key, $this->cache);
    }

    /**
     * @param mixed $key
     * @throws InvalidArgumentException
     */
    private function isValidKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException("Invalid cache key");
        }
    }

    /**
     * @param $keys
     * @throws InvalidArgumentException
     */
    private function isValidKeys($keys)
    {
        if (!is_array($keys) && !($keys instanceof \Traversable)) {
            throw new InvalidArgumentException("Invalid \$keys");
        }
    }
}