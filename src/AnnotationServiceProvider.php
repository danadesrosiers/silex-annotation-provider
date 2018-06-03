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

use Doctrine\Common\Annotations\AnnotationRegistry;
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

        $controllers = [];

        // Process annotations for all controllers in given directory/directories
        if ($app->offsetExists('annot.controllerDir') && !empty($app['annot.controllerDir'])) {
            
            $controllerDir = $app['annot.controllerDir'];
            if (!is_array($controllerDir)) {
                $controllerDir = array($controllerDir);
            }

            $controllers = $annotationService->discoverControllers($controllerDir);
        }

        // Process annotations for any given controllers
        if ($app->offsetExists('annot.controllers') && is_array($app['annot.controllers'])) {
            $controllers = array_merge($controllers, $app['annot.controllers']);
        }

        $annotationService->registerControllers($controllers);
    }

    /**
     * @param Container|Application $app
     */
    public function register(Container $app)
    {
        $app["annot"] = function (Container $app) {
            $cache = $app->offsetExists('annot.cache') ? $app->offsetGet('annot.cache') : null;

            return new AnnotationService($app, $cache, $app['debug']);
        };

        // A custom auto loader for Doctrine Annotations since it can't handle PSR-4 directory structure
        AnnotationRegistry::registerLoader(function ($class) { return class_exists($class); });

        // Register ServiceControllerServiceProvider here so the user doesn't have to.
        $app->register(new ServiceControllerServiceProvider());

        $app['annot.base_uri'] = '';
    }
}
