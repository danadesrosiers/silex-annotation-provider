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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class RequireHttpTest extends \PHPUnit_Framework_TestCase
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
                      "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\Annotations\\RequireHttpTestController")
                  )
        );

        $this->client = new Client($this->app, array('REQUEST_SCHEME' => 'https'));
    }

    public function testHttp()
    {
        $this->client->request("GET", "/test");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHttps()
    {
        // we make the request as http, but it should be redirected to a Http request
        $request = Request::create('https://example.com/test');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://example.com/test'));
    }
}

class RequireHttpTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     * @SLX\RequireHttp
     */
    public function testRequireHttp()
    {
        return new Response();
    }
}