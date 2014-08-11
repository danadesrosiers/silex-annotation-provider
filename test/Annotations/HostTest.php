<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

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

        $this->app->register(
                  new AnnotationServiceProvider(),
                  array(
                      "annot.controllers" => array(
                          "DDesrosiers\\Test\\SilexAnnotations\\Annotations\\HostTestController",
                          "DDesrosiers\\Test\\SilexAnnotations\\Annotations\\HostCollectionTestController"
                      )
                  )
        );
    }

    public function testCorrectHost()
    {
        $this->client = new Client($this->app, array('HTTP_HOST' => 'www.test.com'));
        $this->client->request("GET", "/hostTest");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWrongHost()
    {
        $this->client = new Client($this->app, array('HTTP_HOST' => 'www.wrong.com'));
        $this->client->request("GET", "/hostTest");
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCorrectHostCollection()
    {
        $this->client = new Client($this->app, array('HTTP_HOST' => 'www.test.com'));
        $this->client->request("GET", "/test/hostTest");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWrongHostCollection()
    {
        $this->client = new Client($this->app, array('HTTP_HOST' => 'www.wrong.com'));
        $this->client->request("GET", "/test/hostTest");
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
}

class HostTestController
{
    /**
     * @SLX\Request(method="GET", uri="/hostTest")
     * @SLX\Host("www.test.com")
     */
    public function testHost()
    {
        return new Response();
    }
}

/**
 * @SLX\Controller(prefix="test")
 * @SLX\Host("www.test.com")
 */
class HostCollectionTestController
{
    /**
     * @SLX\Request(method="GET", uri="/hostTest")
     */
    public function testHost()
    {
        return new Response();
    }
}