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

use Silex\ControllerCollection;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class Request
{
    /** @var string */
    public $method;

    /** @var string */
    public $uri;

    /**
     * Register the method $controllerName as controller for the given method and uri.
     *
     * @param \Silex\ControllerCollection $cc
     * @param string                      $controllerName Fully qualified method name of the controller
     * @return \Silex\Controller
     */
    public function process(ControllerCollection $cc, $controllerName)
    {
        return $cc->{strtolower($this->method)}($this->uri, $controllerName);
    }
}
