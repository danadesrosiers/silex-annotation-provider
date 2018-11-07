<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

declare(strict_types=1);

namespace DDesrosiers\SilexAnnotations\Cache;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class AnnotationCache wraps a PSR-16 cache implementation.
 * Uses an array to store data if no cache object is provided.
 *
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class AnnotationCache
{
    /** @var CacheInterface */
    private $cache;

    /** @var array */
    private $data;

    /**
     * @param CacheInterface|null $cache
     */
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