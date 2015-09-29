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
use Doctrine\Common\Annotations\AnnotationRegistry;
use RuntimeException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

/**
 * Class AnnotationServiceProvider provides the 'annot' service, an instance of
 * AnnotationService.
 *
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class AnnotationServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    /**
     * @param \Silex\Application $app
     */
    public function boot(Application $app)
    {
        /** @var AnnotationService $annotationService */
        $annotationService = $app['annot'];

        // Process annotations for all controllers in given directory
        if($app->offsetExists('annot.controllerDir') && is_array($app['annot.controllerDir']) && count($app['annot.controllerDir'])) {
            
            $controllers = array();
            foreach($app['annot.controllerDir'] as $controller_dir) {
                if (!is_dir($controller_dir)) {
                    throw new RuntimeException("Controller directory: {$controller_dir} does not exist.");
                }
                $tmp_controllers = $annotationService->discoverControllers($controller_dir);
                if(is_array($tmp_controllers) && count($tmp_controllers)) {
                    $controllers = array_merge($controllers, $tmp_controllers);
                }                
            }            
            $annotationService->registerControllers($controllers);
            
        } elseif ($app->offsetExists('annot.controllerDir') && strlen($app['annot.controllerDir']) > 0) {
            if (!is_dir($app['annot.controllerDir'])) {
                throw new RuntimeException("Controller directory: {$app['annot.controllerDir']} does not exist.");
            }
            $controllers = $annotationService->discoverControllers($app['annot.controllerDir']);            
            $annotationService->registerControllers($controllers);
        }
        
        // Process annotations for any given controllers
        if ($app->offsetExists('annot.controllers') && is_array($app['annot.controllers'])) {
            foreach ($app['annot.controllers'] as $controllerName) {
                $annotationService->registerController($controllerName);
            }
        }
    }

    /**
     * @param Container|Application $app
     */
    public function register(Container $app)
    {
        if (!$app->offsetExists('annot.useServiceControllers')) {
            $app['annot.useServiceControllers'] = true;
        }

        $app["annot"] = function (Container $app) { return new AnnotationService($app); };

        // A custom auto loader for Doctrine Annotations since it can't handle PSR-4 directory structure
        AnnotationRegistry::registerLoader(function ($class) { return class_exists($class); });

        // Register ServiceControllerServiceProvider here so the user doesn't have to.
        if ($app['annot.useServiceControllers']) {
            $app->register(new ServiceControllerServiceProvider());
        }

        // this service registers the service controller and can be overridden by the user
        $app['annot.registerServiceController'] = $app->protect(
            function ($controllerName) use ($app) {
                if ($app['annot.useServiceControllers']) {
                    $app["$controllerName"] = function (Application $app) use ($controllerName) {
                        return new $controllerName($app);
                    };
                }
            }
        );

        $app['annot.controllerFinder'] = $app->protect(
            function (Application $app, $dir) {
                return $app['annot']->getFiles($dir, $app['annot.controllerNamespace']);
            }
        );

        /** @noinspection PhpUnusedParameterInspection */
        $app['annot.controller_factory'] = $app->protect(
                                               function (Application $app, $controllerName, $methodName, $separator) {
                                                   return $controllerName . $separator . $methodName;
                                               }
        );

        $app['annot.controllerNamespace'] = '';
    }
}
