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
                $cache_class = "Doctrine\\Common\\Cache\\{$app['annot.cache']}Cache";
                if (!class_exists($cache_class)) {
                    throw new RuntimeException("Cache type: [$cache_class] does not exist.  Make sure you include Doctrine cache.");
                }

                $cache = new $cache_class();
            } else {
                throw new RuntimeException("Cache object does not implement Doctrine\\Common\\Cache\\Cache");
            }

            $this->reader = new CachedReader(new AnnotationReader(), $cache, $app['debug']);
        } else {
            $this->reader = new AnnotationReader();
        }
    }

    /**
     * @param string  $controllerName
     * @param boolean $isServiceController
     * @param boolean $newCollection
     * @return \Silex\ControllerCollection
     */
    public function process($controllerName, $isServiceController = true, $newCollection = false)
    {
        $separator = $isServiceController ? ":" : "::";
        $controllerCollection = $newCollection ? $this->app['controllers_factory'] : $this->app['controllers'];
        $reflection_class = new ReflectionClass($controllerName);
        foreach ($reflection_class->getMethods(ReflectionMethod::IS_PUBLIC) as $reflection_method) {
            if (!$reflection_method->isStatic()) {
                $method_annotations = $this->reader->getMethodAnnotations($reflection_method);
                $controllerMethodName = $this->app['annot.controller_factory']($this->app, $controllerName, $reflection_method->getName(), $separator);
                foreach ($method_annotations as $annotation) {
                    if ($annotation instanceof Route) {
                        $annotation->process($controllerCollection, $controllerMethodName);
                    } else if ($annotation instanceof Request) {
                        $controller = $annotation->process($controllerCollection, $controllerMethodName);
                        foreach ($method_annotations as $routeAnnotation) {
                            if ($routeAnnotation instanceof RouteAnnotation) {
                                $routeAnnotation->process($controller);
                            }
                        }
                    }
                }
            }
        }

        return $controllerCollection;
    }

    public function getReader()
    {
        return $this->reader;
    }
}
