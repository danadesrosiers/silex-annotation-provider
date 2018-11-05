<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\AnnotationReader;

use DDesrosiers\SilexAnnotations\Annotations\Controller;
use DDesrosiers\SilexAnnotations\Annotations\Route;

class AnnotationReader
{
    /**
     * @param string $className
     * @return Controller|null
     * @throws \ReflectionException
     */
    public function getControllerAnnotation(string $className): ?Controller
    {
        $reflectionClass = new \ReflectionClass($className);
        $docBlock = new DocBlock($reflectionClass->getDocComment());
        $controllerDefinition = $docBlock->parseAnnotation("Controller");
        if ($controllerDefinition !== null) {
            $prefix = $controllerDefinition['prefix'][0][0] ?? key($controllerDefinition);
            array_shift($controllerDefinition);
            $controller = new Controller($prefix);
            $controller->setModifiers($controllerDefinition);
            foreach ($this->getRouteAnnotations($reflectionClass) as $route) {
                $controller->addRoute($route);
            }
        }

        return $controller ?? null;
    }

    /**
     * @param \ReflectionClass  $class
     * @return Route[]
     */
    private function getRouteAnnotations(\ReflectionClass $class): array
    {
        $routes = [];
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!$method->isStatic()) {
                $docBlock = new DocBlock($method->getDocComment());
                $routeDefinition = $docBlock->parseAnnotation("Route");
                if ($routeDefinition !== null) {
                    $uri = $routeDefinition['uri'][0][0] ?? key($routeDefinition);
                    array_shift($routeDefinition);
                    $route = new Route("$class->name:$method->name", $uri);
                    $route->setModifiers($routeDefinition);
                    $routes[] = $route;
                }
            }
        }

        return $routes;
    }
}