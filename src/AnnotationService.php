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
use Pimple\Container;
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

    /** @var Cache */
    protected $cache;

    /** @var bool */
    protected $useCache;

    const CONTROLLER_CACHE_INDEX = 'annot.controllerFiles';
    /**
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;

        if ($app->offsetExists('annot.cache')) {
            if ($app['annot.cache'] instanceof Cache) {
                $this->cache = $app['annot.cache'];
            } else if (is_string($app['annot.cache']) && strlen($app['annot.cache']) > 0) {
                $cacheClass = "Doctrine\\Common\\Cache\\".$app['annot.cache']."Cache";
                if (!class_exists($cacheClass)) {
                    throw new RuntimeException("Cache type: [$cacheClass] does not exist.  Make sure you include Doctrine cache.");
                }

                $this->cache = new $cacheClass();
            } else {
                throw new RuntimeException("Cache object does not implement Doctrine\\Common\\Cache\\Cache");
            }

            $this->reader = new CachedReader(new AnnotationReader(), $this->cache, $app['debug']);
        } else {
            $this->reader = new AnnotationReader();
        }

        $this->useCache = !$this->app['debug'] && $this->cache instanceof Cache;
    }

    /**
     * @param $dir
     * @return array
     */
    public function discoverControllers($dir)
    {
        $cacheKey = self::CONTROLLER_CACHE_INDEX . ".$dir";

        if ($this->useCache && $this->cache->contains($cacheKey)) {
            $controllerFiles = $this->cache->fetch($cacheKey);
        } else {
            $controllerFiles = $this->app['annot.controllerFinder']($this->app, $dir);

            if ($this->useCache) {
                $this->cache->save($cacheKey, $controllerFiles);
            }
        }

        return $controllerFiles;
    }

    /**
     * @param $controllers
     */
    public function registerControllers($controllers)
    {
        foreach ($controllers as $prefix => $controllerNames) {
            if (!is_array($controllerNames)) {
                $controllerNames = [$controllerNames];
            }
            if (strlen($this->app['annot.base_uri']) === 0 || $this->prefixMatchesUri($prefix)) {
                foreach ($controllerNames as $fqcn) {
                    $this->registerController($fqcn);
                }
            }
        }
    }

    public function prefixMatchesUri($prefix)
    {
        return strpos(
            $_SERVER['REQUEST_URI'],
            $this->cleanPrefix($this->app['annot.base_uri'] . $prefix)
        ) === 0;
    }

    /**
     * Recursively walk the file tree starting from $dir to find potential controller class files.
     * Returns array of fully qualified class names.
     * Namespace detection works with PSR-0 or PSR-4 autoloading.
     *
     * @param string  $dir
     * @param string  $namespace
     * @param array   $files
     * @return array
     */
    public function getFiles($dir, $namespace='', $files=array())
    {
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, array('.', '..'))) {
                    $filePath = "$dir/$entry";
                    if (is_dir($filePath)) {
                        $subNamespace = $namespace ? $namespace."$entry\\" : '';
                        $files = $this->getFiles($filePath, $subNamespace, $files);
                    } else {
                        if (!$namespace) {
                            $namespace = $this->parseNamespace($filePath);
                        }
                        $pathInfo = pathinfo($entry);
                        $className = trim($namespace.$pathInfo['filename']);
                        if (class_exists($className)) {
                            $reflectionClass = new ReflectionClass($className);
                            $annotationClassName = "\\DDesrosiers\\SilexAnnotations\\Annotations\\Controller";
                            $controllerAnnotation = $this->reader->getClassAnnotation($reflectionClass, $annotationClassName);

                            if ($controllerAnnotation instanceof Controller) {
                                $prefix = $this->cleanPrefix("/$controllerAnnotation->prefix");
                                $files[$prefix][] = $className;
                            }
                        }
                    }
                }
            }
            closedir($handle);
        }

        return $files;
    }

    /**
     * Register the controller if a Controller annotation exists in the class doc block or $controllerAnnotation is provided.
     *
     * @param string     $controllerName
     */
    public function registerController($controllerName)
    {
        $reflectionClass = new ReflectionClass($controllerName);
        $annotationClassName = "\\DDesrosiers\\SilexAnnotations\\Annotations\\Controller";
        $controllerAnnotation = $this->reader->getClassAnnotation($reflectionClass, $annotationClassName);

        if ($controllerAnnotation instanceof Controller) {
            $this->app['annot.registerServiceController'](trim($controllerName, "\\"));
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

    /**
     * @param ReflectionClass      $reflectionClass
     * @param ControllerCollection $controllerCollection
     */
    public function processClassAnnotations(ReflectionClass $reflectionClass, ControllerCollection $controllerCollection)
    {
        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof RouteAnnotation) {
                $annotation->process($controllerCollection);
            }
        }
    }

    /**
     * @param ReflectionClass      $reflectionClass
     * @param ControllerCollection $controllerCollection
     */
    public function processMethodAnnotations(ReflectionClass $reflectionClass, ControllerCollection $controllerCollection)
    {
        $separator = $this->app['annot.useServiceControllers'] ? ":" : "::";

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (!$reflectionMethod->isStatic()) {
                $controllerMethodName = $this->app['annot.controller_factory'](
                    $this->app,
                    $reflectionClass->name,
                    $reflectionMethod->name,
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

    /**
     * @return AnnotationReader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Parse the given file to find the namespace.
     *
     * @param $filePath
     * @return string
     */
    protected function parseNamespace($filePath)
    {
        preg_match('/namespace(.*);/', file_get_contents($filePath), $result);
        return isset($result[1]) ? $result[1] . "\\" : '';
    }

    private function cleanPrefix($str)
    {
        return str_replace('//', '/', $str);
    }
}
