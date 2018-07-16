<?php

/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\Annotations;

class Controller
{
    /** @var string */
    public $prefix;

    /** @var string[] */
    private $modifiers;

    /** @var Route[] */
    private $routes;

    public function __construct(?string $prefix = '/')
    {
        $this->prefix = ($prefix[0] !== '/') ? "/$prefix" : $prefix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string[]
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes ?? [];
    }

    /**
     * @param string[] $modifiers
     */
    public function setModifiers(array $modifiers)
    {
        $this->modifiers = $modifiers;
    }

    /**
     * @param Route $route
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }
} 