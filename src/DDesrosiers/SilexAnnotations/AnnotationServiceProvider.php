<?php 
namespace DDesrosiers\SilexAnnotations;

use DDesrosiers\SilexAnnotations\AnnotationService;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\ServiceProviderInterface;

class AnnotationServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
		AnnotationRegistry::registerAutoloadNamespace("DDesrosiers\\SilexAnnotations\\Annotations", $app['annot.srcDir']);
		
		// Process annotations for any given controllers
		if ($app->offsetExists('annot.controllers') && is_array($app['annot.controllers']))
		{
			foreach ($app['annot.controllers'] as $controllerName)
			{
				$app[$controllerName] = $app->share(function(Application $app) use ($controllerName) {
					return new $controllerName($app);
				});

				$app['annot']->process($controllerName);
			}
		}
    }

    public function register(Application $app)
    {
		// Need the ability to register annotation namespace early for ControllerProviders
		if ($app->offsetExists('annot.srcDir'))
		{
			AnnotationRegistry::registerAutoloadNamespace("DDesrosiers\\SilexAnnotations\\Annotations", $app['annot.srcDir']);
		}
		
		// ServiceControllerServiceProvider is required, so register it here so the user doesn't have to.
		$app->register(new ServiceControllerServiceProvider());
		
        $app["annot"] = $app->share(function (Application $app) {
            return new AnnotationService($app);
        });
    }
}
