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
use DDesrosiers\SilexAnnotations\Annotations\Route;
use DDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Pimple\Container;
use Psr\SimpleCache\CacheInterface;
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

    /** @var CacheInterface */
    protected $cache;

    /** @var bool */
    protected $useCache;

    const CONTROLLER_CACHE_INDEX = 'annot.controllerFiles';

    /**
     * @param Container  $app
     * @param CacheInterface|null $cache
     * @param bool       $debug
     */
    public function __construct(Container $app, CacheInterface $cache = null, bool $debug = false)
    {
        $this->app = $app;
        $this->cache = $cache;
        $this->reader = new AnnotationReader();

        $this->useCache = !$debug && $this->cache instanceof CacheInterface;
    }

    /**
     * @param string[] $controllerDirs
     * @return string[][]
     */
    public function discoverControllers(array $controllerDirs): array
    {
        if ($this->useCache && $this->cache->has(self::CONTROLLER_CACHE_INDEX)) {
            $controllers = $this->cache->get(self::CONTROLLER_CACHE_INDEX);
        } else {
            $controllers = [];
            foreach ($controllerDirs as $dir) {
                foreach ($this->getFiles($dir) as $className) {
                    if (class_exists($className)) {
                        $controllerAnnotation = $this->getControllerAnnotation($className);
                        if ($controllerAnnotation instanceof Controller) {
                            $controllers[$controllerAnnotation->getPrefix()][] = $className;
                        }
                    }
                }
            }

            if ($this->useCache) {
                $this->cache->set(self::CONTROLLER_CACHE_INDEX, $controllers);
            }
        }

        return $controllers;
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
            foreach ($controllerNames as $fqcn) {
                if (strlen($prefix) == 0 || $this->prefixMatchesUri($prefix)) {
                    $this->registerController($fqcn);
                }
            }
        }
    }

    public function prefixMatchesUri($prefix)
    {
        return ($this->app->offsetExists('annot.base_uri')
            && strpos($_SERVER['REQUEST_URI'], $this->app['annot.base_uri'].$prefix) === 0);
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
    public function getFiles(string $dir, string $namespace='', $files=[]): array
    {
        if (!is_dir($dir)) {
            throw new RuntimeException("Controller directory: {$dir} does not exist.");
        }

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
                        $files[] = trim($namespace.pathinfo($entry)['filename']);
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
            $controllerName = trim($controllerName, "\\");
            $this->app["$controllerName"] = function (Application $app) use ($controllerName) {
                return new $controllerName($app);
            };
            $controllerAnnotation->process($this->app, $reflectionClass);
        }
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
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (!$reflectionMethod->isStatic()) {
                $controllerMethodName = "$reflectionClass->name:$reflectionMethod->name";
                $methodAnnotations = $this->reader->getMethodAnnotations($reflectionMethod);
                foreach ($methodAnnotations as $annotation) {
                    if ($annotation instanceof Route) {
                        $annotation->process($controllerCollection, $controllerMethodName, $this->app);
                    }
                }
            }
        }
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

    /**
     * @param $className
     * @return null|object
     * @throws \ReflectionException
     */
    private function getControllerAnnotation($className): ?Controller
    {
        $reflectionClass = new ReflectionClass($className);
        $annotationClassName = "\\DDesrosiers\\SilexAnnotations\\Annotations\\Controller";
        $controllerAnnotation = $this->reader->getClassAnnotation($reflectionClass, $annotationClassName);

        return $controllerAnnotation;
    }
}
