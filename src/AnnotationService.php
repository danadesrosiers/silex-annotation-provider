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

        foreach ($this->app['annot.fileIterator']($dir) as $fileName => $file) {
            preg_match('/namespace(.*);/', file_get_contents($fileName), $result);
            $fqcn = trim($result[1] . "\\" . basename($fileName, ".php"));
            if (class_exists($fqcn)) {
                $this->registerController($fqcn);
            }
        }
    }

    /**
     * Register the controller if a Controller annotation exists in the class doc block or $controllerAnnotation is provided.
     *
     * @param string     $controllerName
     * @param Controller $defaultControllerAnnotation (optional) - For legacy controller classes that don't have a Controller Annotation
     */
    public function registerController($controllerName, Controller $defaultControllerAnnotation = null)
    {
        $reflectionClass = new ReflectionClass($controllerName);
        $annotationClassName = "\\DDesrosiers\\SilexAnnotations\\Annotations\\Controller";
        $controllerAnnotation = $this->reader->getClassAnnotation($reflectionClass, $annotationClassName);

        if (!($controllerAnnotation instanceof Controller)) {
            $controllerAnnotation = $defaultControllerAnnotation;
        }

        if ($controllerAnnotation instanceof Controller) {
            $this->app['annot.registerServiceController']($controllerName);
            $controllerAnnotation->process($this->app, $reflectionClass);
        }
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
                        $annotation->process($controllerCollection, $controllerMethodName, $this->app);
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
