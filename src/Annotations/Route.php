<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\Annotations;

class Route
{
    /** @var string */
    public $controllerName;

    /** @var string */
    public $method;

    /** @var string */
    public $uri;

    /** @var string[][] */
    public $modifiers;

    public function __construct(string $controllerName, string $uri)
    {
        $this->controllerName = $controllerName;
        $uri = explode(' ', $uri);
        $this->uri = array_pop($uri);
        $this->method = count($uri) > 0 ? array_shift($uri) : 'MATCH';
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * @return string[][]
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    /**
     * @param string[][] $modifiers
     */
    public function setModifiers(array $modifiers)
    {
        $this->modifiers = $modifiers;
    }
}