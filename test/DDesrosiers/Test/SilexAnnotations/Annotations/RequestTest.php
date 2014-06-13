<?php

namespace DDesrosiers\Test\SilexAnnotations\Annotations;

use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Silex\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\Routing\RouteCollection;

class RequestTest extends \PHPUnit_Framework_TestCase
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
            "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\Annotations\\RequestTestController")
        ));

        $this->client = new Client($this->app);
    }

    public function test1Request()
    {
        $this->client->request("GET", "/oneRequest");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testPost()
    {
        $this->client->request("POST", "/post");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testPut()
    {
        $this->client->request("PUT", "/put");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testDelete()
    {
        $this->client->request("DELETE", "/delete");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testMatch()
    {
        $this->client->request("GET", "/match");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());

        $this->client->request("POST", "/match");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testMultipleRequests()
    {
        $this->client->request("GET", "/firstRequest");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());

        $this->client->request("GET", "/secondRequest");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testMultipleRequestsShareModifiers()
    {
        $this->client->request("GET", "/firstRequest/asdf");
        $response = $this->client->getResponse();
        $this->assertEquals('404', $response->getStatusCode());

        $this->client->request("GET", "/secondRequest/asdf");
        $response = $this->client->getResponse();
        $this->assertEquals('404', $response->getStatusCode());

        /** @var RouteCollection $routes */
        $routes = $this->app['routes'];
        /** @var Route[] $iterator */
        $iterator = $routes->getIterator();

        $firstRoute = null;
        $secondRoute = null;
        foreach ($iterator as $route)
        {
            if ($route->getPath() == "/firstRequest/{num}")
            {
                $firstRoute = $route;
            }
            else if ($route->getPath() == "/secondRequest/{num}")
            {
                $secondRoute = $route;
            }
        }

        $this->assertInstanceOf('Silex\Route', $firstRoute);
        $this->assertEquals('\d+', $firstRoute->getRequirement('num'));
        $this->assertInstanceOf('Silex\Route', $secondRoute);
        $this->assertEquals('\d+', $secondRoute->getRequirement('num'));
    }
}

class RequestTestController
{
    /**
     * @SLX\Request(method="GET", uri="oneRequest")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function test1request()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="firstRequest")
     * @SLX\Request(method="GET", uri="secondRequest")
     * @return Response
     */
    public function testMultipleRequests()
    {
        return new Response();
    }

    /**
     * The assert modifier should be applied to both endpoints.
     *
     * @SLX\Request(method="GET", uri="firstRequest/{num}")
     * @SLX\Request(method="GET", uri="secondRequest/{num}")
     * @SLX\Assert(variable="num", regex="\d+")
     * @return Response
     */
    public function testMultipleRequestsShareModifiers()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="POST", uri="/post")
     */
    public function testPostRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="PUT", uri="/put")
     */
    public function testPutRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="DELETE", uri="/delete")
     */
    public function testDeleteRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="MATCH", uri="/match")
     */
    public function testMatchRequest()
    {
        return new Response();
    }
}