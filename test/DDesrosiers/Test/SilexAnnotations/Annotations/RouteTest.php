<?php

namespace DDesrosiers\Test\SilexAnnotations\Annotations;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    protected $app;

    /** @var Client */
    protected $client;

    public function setUp()
    {
        $this->app = new Application();
        $this->app['debug'] = true;

        $this->app->register(new AnnotationServiceProvider(), array(
            "annot.srcDir" => __DIR__."/../../../../../src",
            "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\Annotations\\RouteTestController")
        ));

        $this->client = new Client($this->app);
    }

    public function testMultipleRoutes()
    {
        $this->client->request("GET", "/test/45");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());

        $this->client->request("GET", "/test2/45");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testIsolationOfModifiers()
    {
        // The assert should not be applied to the test2 uri, so a string for {var} should match the route.
        $this->client->request("GET", "/test2/string");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }
}

class RouteTestController
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/test/{var}"),
     *      @SLX\Assert(variable="var", regex="\d+")
     * )
     *
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/test2/{var}")
     * )
     */
    public function routeTest($var)
    {
        return new Response($var);
    }
}