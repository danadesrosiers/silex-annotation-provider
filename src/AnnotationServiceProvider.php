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
        // Process annotations for any given controllers
        if ($app->offsetExists('annot.controllers') && is_array($app['annot.controllers'])) {
            foreach ($app['annot.controllers'] as $groupName => $controllerGroup) {
                if (!is_array($controllerGroup)) {
                    $controllerGroup = array($controllerGroup);
                }

                foreach ($controllerGroup as $controllerName) {
                    $app["$controllerName"] = $app->share(
                                                  function (Application $app) use ($controllerName) {
                                                      return new $controllerName($app);
                                                  }
                    );

                    $isCollection = !is_numeric($groupName);
                    /** @var AnnotationService $annotationService */
                    $annotationService = $app['annot'];
                    $collection = $annotationService->process($controllerName, true, $isCollection);
                    if ($isCollection) {
                        $app->mount($groupName, $collection);
                    }
                }
            }
        }
    }

    /**
     * @param \Silex\Application $app
     */
    public function register(Application $app)
    {
        // A custom auto loader for Doctrine Annotations since it can't handle PSR-4 directory structure
        AnnotationRegistry::registerLoader(function ($class) {
            $annotationDir = __DIR__ . "/Annotations";
            $fqcn_array = explode("\\", $class);
            $class_name = array_pop($fqcn_array);
            if (is_file("$annotationDir/$class_name.php"))
            {
                /** @noinspection PhpIncludeInspection */
                require_once "$annotationDir/$class_name.php";
                return true;
            }
            return false;
        });

        // ServiceControllerServiceProvider is required, so register it here so the user doesn't have to.
        $app->register(new ServiceControllerServiceProvider());

        $app["annot"] = $app->share(
                            function (Application $app) {
                                return new AnnotationService($app);
                            }
        );
    }
}
