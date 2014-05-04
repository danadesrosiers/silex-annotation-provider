<?php

namespace DDesrosiers\SilexAnnotations\Test\Annotations;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class ValueTest extends \PHPUnit_Framework_TestCase
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
            "annot.srcDir" => __DIR__."/../../../../../../src",
            "annot.controllers" => array("DDesrosiers\\SilexAnnotations\\Test\\Annotations\\ValueTestController")
        ));

        $this->client = new Client($this->app);
    }

    public function testDefaultValue()
    {
        $this->client->request("GET", "/test");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->client->request("GET", "/");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('default', $response->getContent());
    }
}

class ValueTestController
{
    /**
     * @SLX\Request(method="GET", uri="/{var}")
     * @SLX\Value(variable="var", default="default")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }
}