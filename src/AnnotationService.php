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
use Pimple\Container;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
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

    const CONTROLLER_CACHE_INDEX = 'annot.controllerFiles';

    /**
     * @param Container  $app
     * @param CacheInterface $cache
     */
    public function __construct(Container $app, CacheInterface $cache)
    {
        $this->app = $app;
        $this->cache = $cache;
        $this->reader = new AnnotationReader();
    }

    /**
     * @param string $controllerDir
     * @param array $controllerClassNames
     * @throws InvalidArgumentException
     */
    public function registerControllers(string $controllerDir = null, array $controllerClassNames = [])
    {
        $controllers = $this->fetchCache(
            self::CONTROLLER_CACHE_INDEX,
            function () use ($controllerDir, $controllerClassNames) {
                $potentialControllers = array_merge($this->discoverControllers($controllerDir), $controllerClassNames);
                foreach ($potentialControllers as $className) {
                    $controller = $this->getControllerAnnotation($className);
                    if ($controller instanceof Controller) {
                        $controllers[$controller->getPrefix()][] = $className;
                    }
                }

                return $controllers ?? [];
            }
        );

        foreach ($controllers as $prefix => $controllerGroup) {
            if ($this->prefixMatchesUri($prefix)) {
                foreach ($controllerGroup as $controllerClassName) {
                    $this->registerController($controllerClassName);
                }
            }
        }
    }

    /**
     * @param string $controllerDir
     * @return string[]
     */
    public function discoverControllers(string $controllerDir): array
    {
        $controllers = [];
        foreach ($this->getFiles($controllerDir) as $className) {
            if (class_exists($className)) {
                $controllers[] = $className;
            }
        }

        return $controllers;
    }

    /**
     * @param string $controllerClassName
     * @return Controller|null
     * @throws InvalidArgumentException
     */
    private function getControllerAnnotation(string $controllerClassName): ?Controller
    {
        return $this->fetchCache($controllerClassName, function () use ($controllerClassName) {
            return $this->reader->getControllerAnnotation($controllerClassName);
        });
    }

    private function prefixMatchesUri($prefix)
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
    private function getFiles(string $dir, string $namespace='', $files=[]): array
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
    /**
     * Parse the given file to find the namespace.
     *
     * @param $filePath
     * @return string
     */
    private function parseNamespace($filePath)
    {
        preg_match('/namespace(.*);/', file_get_contents($filePath), $result);
        return isset($result[1]) ? $result[1] . "\\" : '';
    }

    /**
     * @param string   $key
     * @param \Closure $closure
     * @return mixed|null
     * @throws InvalidArgumentException
     */
    private function fetchCache(string $key, \Closure $closure)
    {
        $data = $this->cache->get($key);
        if ($data === null) {
            $data = $closure();
            $this->cache->set($key, $data);
        }

        return $data;
    }
}
