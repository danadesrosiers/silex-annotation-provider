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
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class Host implements RouteAnnotation
{
    /** @var string */
    public $host;

    /**
     * @inheritdoc
     */
    public function process($route)
    {
        /** @var \Silex\Route $route */
        $route->host($this->host);
    }
}
