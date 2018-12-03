<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

declare(strict_types=1);

namespace DDesrosiers\SilexAnnotations;

use DDesrosiers\SilexAnnotations\AnnotationReader\AnnotationReader;
use DDesrosiers\SilexAnnotations\Cache\AnnotationCache;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\SimpleCache\InvalidArgumentException;
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
     * @param Application $app
     * @throws InvalidArgumentException
     */
    public function boot(Application $app)
    {
        /** @var AnnotationService $annotationService */
        $annotationService = $app['annot'];
        $annotationService->registerControllers();
    }

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app["annot"] = function (Application $app) {
            $cache = (!$app['debug'] && $app->offsetExists('annot.cache')) ? $app['annot.cache'] : null;
            return new AnnotationService(
                $app,
                new ControllerFinder($app['annot.controllerDir'], $app['annot.controllers']),
                new AnnotationReader(),
                new AnnotationCache($cache));
        };

        $app->register(new ServiceControllerServiceProvider());

        $app['annot.controllers'] = [];
        $app['annot.controllerDir'] = null;
    }
}
