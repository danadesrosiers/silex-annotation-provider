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

namespace DDesrosiers\SilexAnnotations;

use DDesrosiers\SilexAnnotations\AnnotationReader\AnnotationReader;
use DDesrosiers\SilexAnnotations\Annotations\Controller;
use DDesrosiers\SilexAnnotations\Cache\AnnotationCache;
use Psr\SimpleCache\InvalidArgumentException;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AnnotationService parses annotations on classes and converts them to
 * Silex routes.
 *
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class AnnotationService
{
    /** @var Application */
    private $app;

    /** @var ControllerFinder */
    private $controllerFinder;

    /** @var AnnotationReader */
    private $reader;

    /** @var AnnotationCache */
    private $cache;

    const CONTROLLER_CACHE_INDEX = 'annot.controllerFiles';

    /**
     * @param Application      $app
     * @param ControllerFinder $finder
     * @param AnnotationReader $reader
     * @param AnnotationCache  $cache
     */
    public function __construct(
        Application $app,
        ControllerFinder $finder,
        AnnotationReader $reader,
        AnnotationCache $cache
    ) {
        $this->app = $app;
        $this->controllerFinder = $finder;
        $this->cache = $cache;
        $this->reader = $reader;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function registerControllers()
    {
        $controllers = $this->cache->fetch(self::CONTROLLER_CACHE_INDEX, function () {
            $controllers = [];
            foreach ($this->controllerFinder->getControllerClasses() as $className) {
                $controller = $this->getControllerAnnotation($className);
                if ($controller instanceof Controller) {
                    $controllers[$controller->getPrefix()][] = $className;
                }
            }
            return $controllers;
        });

        $request = new Request([], [], [], [], [], $_SERVER);
        foreach ($controllers as $prefix => $controllerGroup) {
            // register the controller only if the prefix matches the URI
            if (strpos($request->getPathInfo(), $prefix) === 0) {
                foreach ($controllerGroup as $controllerClassName) {
                    $this->registerController($controllerClassName);
                }
            }
        }
    }

    /**
     * @param string $controllerClassName
     * @return Controller|null
     * @throws InvalidArgumentException
     */
    private function getControllerAnnotation(string $controllerClassName): ?Controller
    {
        return $this->cache->fetch($controllerClassName, function () use ($controllerClassName) {
            return $this->reader->getControllerAnnotation($controllerClassName);
        });
    }

    /**
     * Register the controller using the controller definition parsed from annotation.
     *
     * @param string $controllerClassName
     * @throws InvalidArgumentException
     */
    private function registerController(string $controllerClassName)
    {
        $controllerAnnotation = $this->getControllerAnnotation($controllerClassName);

        if ($controllerAnnotation instanceof Controller) {
            $controllerName = trim($controllerClassName, "\\");
            $this->app["$controllerName"] = function (Application $app) use ($controllerName) {
                return new $controllerName($app);
            };

            $this->processClassAnnotation($controllerAnnotation);
        }
    }

    /**
     * @param Controller $controllerAnnotation
     */
    private function processClassAnnotation(Controller $controllerAnnotation)
    {
        /** @var ControllerCollection $controllerCollection */
        $controllerCollection = $this->app['controllers_factory'];

        foreach ($controllerAnnotation->getModifiers() as $name => $values) {
            foreach ($values as $value) {
                $controllerCollection->$name(...$value);
            }
        }

        foreach ($controllerAnnotation->getRoutes() as $route) {
            $controller = $controllerCollection->match($route->getUri(), $route->getControllerName());
            if (($method = strtoupper($route->getMethod())) != 'MATCH') {
                $controller->method($method);
            }
            foreach ($route->getModifiers() as $name => $values) {
                foreach ($values as $value) {
                    $controller->$name(...$value);
                }
            }
        }

        $this->app->mount($controllerAnnotation->getPrefix(), $controllerCollection);
    }
}
