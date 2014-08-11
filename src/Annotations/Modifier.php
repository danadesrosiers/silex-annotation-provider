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

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Modifier implements RouteAnnotation
{
    /** @var string */
    public $method;

    /** @var array */
    public $args;

    /**
     * @inheritdoc
     * @throws \RuntimeException
     */
    public function process($controller)
    {
        try {
            call_user_func_array(array($controller, $this->method), $this->args ? : array());
        } catch (\BadMethodCallException $ex) {
            throw new \RuntimeException("Modifier: [$this->method] does not exist.");
        }
    }
}

