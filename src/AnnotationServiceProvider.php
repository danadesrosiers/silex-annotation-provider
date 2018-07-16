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

use DDesrosiers\SilexAnnotations\Cache\MemoCache;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\SimpleCache\CacheInterface;
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function boot(Application $app)
    {
        /** @var AnnotationService $annotationService */
        $annotationService = $app['annot'];

        $controllerDir = $app->offsetExists('annot.controllerDir') ? $app['annot.controllerDir'] : null;
        $controllers = $app->offsetExists('annot.controllers') ? $app['annot.controllers'] : [];

        $annotationService->registerControllers($controllerDir, $controllers);
    }

    /**
     * @param Container|Application $app
     */
    public function register(Container $app)
    {
        $app["annot"] = function (Container $app) {
            $cache = $app->offsetExists('annot.cache') ? $app->offsetGet('annot.cache') : null;
            if ($app['debug'] || !($cache instanceof CacheInterface)) {
                $cache = new MemoCache();
            }

            return new AnnotationService($app, $cache);
        };

        // Register ServiceControllerServiceProvider here so the user doesn't have to.
        $app->register(new ServiceControllerServiceProvider());

        $app['annot.base_uri'] = '';
    }
}
