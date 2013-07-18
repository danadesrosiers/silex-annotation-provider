<?php 
namespace DJD\Silex\Provider;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\ServiceProviderInterface;

class AnnotationServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
        AnnotationRegistry::registerAutoloadNamespace("DJD\Silex\Annotations", $srcDir);
    }

    public function register(Application $app)
    {
        $app["annot"] = $app->share(function (Application $app) {
            return new AnnotationService();
        });
    }
}
