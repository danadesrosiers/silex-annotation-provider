<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationService;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Doctrine\Common\Cache\ApcCache;
use RuntimeException;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class AnnotationServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    protected $app;

    public function setUp()
    {
        $this->app = new Application();
        $this->app['debug'] = true;
    }

    public function testRegisterController()
    {
        $this->app->register( new AnnotationServiceProvider(), array("annot.controllerDir" => __DIR__."/Controller"));

        $client = new Client($this->app);
        $client->request('GET', '/test/test1');

        $response = $client->getResponse();

        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testServiceController()
    {
        $this->app->register(
                  new AnnotationServiceProvider(),
                  array(
                      "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\TestController")
                  )
        );

        $client = new Client($this->app);
        $client->request("GET", "/test1");
        $response = $client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testIsolationOfControllerModifiers()
    {
        $this->app->register(
                  new AnnotationServiceProvider(),
                      array(
                          "annot.controllers" => array(
                              "DDesrosiers\\Test\\SilexAnnotations\\TestController",
                              "DDesrosiers\\Test\\SilexAnnotations\\TestControllerWithNoPrefix"
                          )
                      )
        );

        $client = new Client($this->app);

        $client->request("GET", "/test");
        $response = $client->getResponse();
        $this->assertEquals('500', $response->getStatusCode());

        $client->request("GET", "/test1");
        $response = $client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testControllerProvider()
    {
        $this->app->register(new AnnotationServiceProvider());
        $this->app->mount('/cp', new TestControllerProvider());

        $client = new Client($this->app);
        $client->request("GET", "/cp/test");

        $response = $client->getResponse();

        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testCacheUsingStringIdentifier()
    {
        $this->app->register(
                  new AnnotationServiceProvider(),
                  array(
                      "annot.cache"       => 'Array',
                      "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\TestController")
                  )
        );

        /** @var AnnotationService $service */
        $service = $this->app['annot'];
        $this->assertInstanceOf("Doctrine\\Common\\Annotations\\CachedReader", $service->getReader());
    }

    public function testCacheUsingImplementationOfCache()
    {
        $this->app->register(
                  new AnnotationServiceProvider(),
                  array(
                      "annot.cache"       => new ApcCache(),
                      "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\TestController")
                  )
        );

        /** @var AnnotationService $service */
        $service = $this->app['annot'];
        $this->assertInstanceOf("Doctrine\\Common\\Annotations\\CachedReader", $service->getReader());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidCacheString()
    {
        $this->app->register(
                  new AnnotationServiceProvider(),
                  array(
                      "annot.cache"       => 'Fake',
                      "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\TestController")
                  )
        );
        $this->app['annot'];
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidCacheClass()
    {
        $this->app->register(
                  new AnnotationServiceProvider(),
                  array(
                      "annot.cache"       => new InvalidCache(),
                      "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\TestController")
                  )
        );
        $this->app['annot'];
    }
}

class TestController
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test1")
     * )
     */
    public function test1()
    {
        return new Response();
    }

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test2/{num}"),
     *      @SLX\RequireHttp
     * )
     */
    public function testMethod($num)
    {
        return new Response("success $num");
    }

    public function beforeCallback()
    {
        throw new \Exception("before callback");
    }
}

/**
 * @SLX\Controller(prefix="/test")
 */
class TestControllerWithControllerAnnotation
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test1")
     * )
     */
    public function test1()
    {
        return new Response();
    }
}

/**
 * @SLX\Controller
 * @SLX\Before("DDesrosiers\SilexAnnotations\Test\Annotations\BeforeTestController::beforeCallback")
 */
class TestControllerWithNoPrefix
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    public static function beforeCallback()
    {
        throw new \Exception("before callback");
    }
}

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

class InvalidCache
{

}