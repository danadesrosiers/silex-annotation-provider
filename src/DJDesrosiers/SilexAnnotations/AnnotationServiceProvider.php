<?php 
namespace DJDesrosiers\SilexAnnotations;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\ServiceProviderInterface;
use DJDesrosiers\SilexAnnotations\AnnotationService;

class AnnotationServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
		// TODO: Allow user to specify their own annotations.
        AnnotationRegistry::registerAutoloadNamespace("DJDesrosiers\SilexAnnotations\Annotations", $app['annot.srcDir']);
		foreach ($app['annot.controllers'] as $controllerName)
		{
			$app[$controllerName] = $app->share(function(Application $app) use ($controllerName) {
				return new $controllerName($app);
			});

			$app['annot']->registerController($controllerName);
		}
    }

    public function register(Application $app)
    {
        $app["annot"] = $app->share(function (Application $app) {
            return new AnnotationService($app);
        });
    }
}
