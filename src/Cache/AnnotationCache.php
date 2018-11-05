<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\Cache;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class AnnotationCache
{
    /** @var CacheInterface */
    private $cache;

    private $data;

    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws InvalidArgumentException
     */
    public function get($key)
    {
        return $this->cache ? $this->cache->get($key) : $this->data[$key] ?? null;
    }

    /**
     * @param $key
     * @param $data
     * @throws InvalidArgumentException
     */
    public function set($key, $data)
    {
        if ($this->cache) {
            $this->cache->set($key, $data);
        } else {
            $this->data[$key] = $data;
        }
    }

    /**
     * @param string   $key
     * @param \Closure $closure
     * @return mixed|null
     * @throws InvalidArgumentException
     */
    public function fetch(string $key, \Closure $closure)
    {
        $data = $this->get($key);
        if ($data === null) {
            $this->set($key, $data = $closure());
        }

        return $data;
    }
}