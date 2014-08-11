<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations;

use DDesrosiers\SilexAnnotations\Annotations\Controller;
use DDesrosiers\SilexAnnotations\Annotations\Request;
use DDesrosiers\SilexAnnotations\Annotations\Route;
use DDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\Cache;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Silex\Application;
use Silex\ControllerCollection;

/**
 * Class AnnotationService parses annotations on classes and converts them to
 * Silex routes.
 *
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class AnnotationService
{
    /** @var Application */
    protected $app;

    /** @var AnnotationReader */
    protected $reader;

    /**
     * @param \Silex\Application $app
     * @throws RuntimeException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        if ($app->offsetExists('annot.cache')) {
            if ($app['annot.cache'] instanceof Cache) {
                $cache = $app['annot.cache'];
            } else if (is_string($app['annot.cache']) && strlen($app['annot.cache']) > 0) {
                $cacheClass = "Doctrine\\Common\\Cache\\{$app['annot.cache']}Cache";
                if (!class_exists($cacheClass)) {
                    throw new RuntimeException("Cache type: [$cacheClass] does not exist.  Make sure you include Doctrine cache.");
                }

                $cache = new $cacheClass();
            } else {
                throw new RuntimeException("Cache object does not implement Doctrine\\Common\\Cache\\Cache");
            }

            $this->reader = new CachedReader(new AnnotationReader(), $cache, $app['debug']);
        } else {
            $this->reader = new AnnotationReader();
        }
    }

    public function discoverControllers($dir)
    {
        if (!is_dir($dir)) {
            throw new RuntimeException("Controller directory: {$dir} does not exist.");
        }

        $controllers = array();
        $fileIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($fileIterator as $fileName => $file) {
            list ($name, $extension) = explode('.', $fileName);
            if (in_array($extension, array('php', 'phtml'))) {
                $parser = new ClassParser($fileName);
                foreach ($parser->parse() as $className) {
                    $reflectionClass = new ReflectionClass($className);
                    $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);
                    foreach ($classAnnotations as $annotation) {
                        if ($annotation instanceof Controller) {
                            $annotation->process($this->app, $reflectionClass);
                        }
                    }
                }
            }
        }

        return $controllers;
    }

    public function registerController($controllerName, $mountPrefix = null)
    {
        if ($this->app['annot.useServiceControllers']) {
            $this->app["$controllerName"] = $this->app->share(
                                                      function (Application $app) use ($controllerName) {
                                                          return new $controllerName($app);
                                                      }
            );
        }

        $reflectionClass = new ReflectionClass($controllerName);
        $controllerAnnotation = $this->reader->getClassAnnotation(
                                             $reflectionClass,
                                             "\\DDesrosiers\\SilexAnnotations\\Annotations\\Controller"
        );
        if (!($controllerAnnotation instanceof Controller)) {
            $controllerAnnotation = new Controller();
            if (!is_null($mountPrefix)) {
                $controllerAnnotation->prefix = is_int($mountPrefix) ? null : $mountPrefix;
            }
        }

        $controllerAnnotation->process($this->app, $reflectionClass);
    }

    /**
     * @param string $controllerName
     * @param boolean $isServiceController
     * @param boolean $newCollection
     * @return \Silex\ControllerCollection
     */
    public function process($controllerName, $isServiceController = true, $newCollection = false)
    {
        $this->app['annot.useServiceControllers'] = $isServiceController;
        $controllerCollection = $newCollection ? $this->app['controllers_factory'] : $this->app['controllers'];
        $reflectionClass = new ReflectionClass($controllerName);

        $this->processMethodAnnotations($reflectionClass, $controllerCollection);

        return $controllerCollection;
    }

    public function processClassAnnotations(ReflectionClass $reflectionClass, ControllerCollection $controllerCollection)
    {
        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof RouteAnnotation) {
                $annotation->process($controllerCollection);
            }
        }
    }

    public function processMethodAnnotations(ReflectionClass $reflectionClass, ControllerCollection $controllerCollection)
    {
        $separator = $this->app['annot.useServiceControllers'] ? ":" : "::";

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (!$reflectionMethod->isStatic()) {
                $controllerMethodName = $this->app['annot.controller_factory'](
                    $this->app,
                    $reflectionClass->getName(),
                    $reflectionMethod->getName(),
                    $separator
                );
                $methodAnnotations = $this->reader->getMethodAnnotations($reflectionMethod);
                foreach ($methodAnnotations as $annotation) {
                    if ($annotation instanceof Route) {
                        $annotation->process($controllerCollection, $controllerMethodName);
                    } else if ($annotation instanceof Request) {
                        $controller = $annotation->process($controllerCollection, $controllerMethodName);
                        foreach ($methodAnnotations as $routeAnnotation) {
                            if ($routeAnnotation instanceof RouteAnnotation) {
                                $routeAnnotation->process($controller);
                            }
                        }
                    }
                }
            }
        }
    }

    public function getReader()
    {
        return $this->reader;
    }


}
