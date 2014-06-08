<?php

namespace DDesrosiers\Test\SilexAnnotations\Annotations;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class HostTest extends \PHPUnit_Framework_TestCase
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
            "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\Annotations\\HostTestController")
        ));

        $this->client = new Client($this->app, array('HTTP_HOST' => 'www.test.com'));
    }

    public function testHost()
    {
        $this->client->request("GET", "/rightHost");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->client->request("GET", "/wrongHost");
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
}

class HostTestController
{
    /**
     * @SLX\Request(method="GET", uri="/rightHost")
     * @SLX\Host("www.test.com")
     */
    public function testHost()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/wrongHost")
     * @SLX\Host("www.wrong.com")
     */
    public function testWrongHost()
    {
        return new Response();
    }
}