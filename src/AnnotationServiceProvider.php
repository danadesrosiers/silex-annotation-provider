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
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\ServiceProviderInterface;

/**
 * Class AnnotationServiceProvider provides the 'annot' service, an instance of
 * AnnotationService.
 *
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class AnnotationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Silex\Application $app
     */
    public function boot(Application $app)
    {
        /** @var AnnotationService $annotationService */
        $annotationService = $app['annot'];

        // Process annotations for all controllers in given directory
        if ($app->offsetExists('annot.controllerDir') && strlen($app['annot.controllerDir']) > 0) {
            if (!is_dir($app['annot.controllerDir'])) {
                throw new RuntimeException("Controller directory: {$app['annot.controllerDir']} does not exist.");
            }
            $controllers = $annotationService->discoverControllers($app['annot.controllerDir']);
            $annotationService->registerControllers($controllers);
        }

        // Process annotations for any given controllers
        if ($app->offsetExists('annot.controllers') && is_array($app['annot.controllers'])) {
            foreach ($app['annot.controllers'] as $groupName => $controllerGroup) {
                if (!is_array($controllerGroup)) {
                    $controllerGroup = array($controllerGroup);
                }

                foreach ($controllerGroup as $controllerName) {
                    $controllerAnnotation = new Controller();
                    if (!is_int($groupName)) {
                        $controllerAnnotation->prefix = $groupName;
                    }
                    $annotationService->registerController($controllerName, $controllerAnnotation);
                }
            }
        }
    }

    /**
     * @param \Silex\Application $app
     */
    public function register(Application $app)
    {
        if (!$app->offsetExists('annot.useServiceControllers')) {
            $app['annot.useServiceControllers'] = true;
        }

        $app["annot"] = $app->share(function (Application $app) { return new AnnotationService($app); });

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
                    $app["$controllerName"] = $app->share(
                        function (Application $app) use ($controllerName) {
                            return new $controllerName($app);
                        }
                    );
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
