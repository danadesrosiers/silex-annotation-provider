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

use DDesrosiers\SilexAnnotations\AnnotationService;
use ReflectionClass;
use Silex\Application;

/**
 * @Annotation
 * @Target("CLASS")
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class Controller
{
    public $prefix;

    public function process(Application $app, ReflectionClass $reflectionClass)
    {
        $controllerCollection = $app['controllers_factory'];
        /** @var AnnotationService $annotationService */
        $annotationService = $app['annot'];
        $annotationService->processClassAnnotations($reflectionClass, $controllerCollection);
        $annotationService->processMethodAnnotations($reflectionClass, $controllerCollection);
        $app->mount($this->prefix, $controllerCollection);
    }

    public function getPrefix()
    {
        // the prefix might not start with a forward slash, but the REQUEST_URI always will
        // make sure we always have a forward slash so the comparison to REQUEST_URI works as expected
        return ($this->prefix[0] !== '/') ? "/$this->prefix" : $this->prefix;
    }
} 