<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationService;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

class TestControllerProvider implements ControllerProviderInterface
{
    function connect(Application $app)
    {
        /** @var AnnotationService $annotationService */
        $annotationService = $app['annot'];
        return $annotationService->process(get_class($this), false, true);
    }

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test")
     * )
     */
    public function testMethod()
    {
        return new Response("test Method");
    }
}
