<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations\Annotations;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class ModifierTest extends \PHPUnit_Framework_TestCase
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
            "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\Annotations\\ModifierTestController")
        ));

        $this->client = new Client($this->app, array('HTTP_HOST' => 'www.test.com'));
    }

    public function testHostOneArg()
    {
        // testing a modifier that has one argument
        $this->client->request("GET", "/host");
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testAssertMultipleArgs()
    {
        // testing a modifier that has more than one argument
        $this->client->request("GET", "/assert/fail");
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testHttpsNoArgs()
    {
        // testing a modifier that has no arguments
        // we make the request as http, but it should be redirected to a Http request
        $request = Request::create('http://test.com/requirehttps');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://test.com/requirehttps'));
    }
}

class ModifierTestController
{
    /**
     * @SLX\Request(method="GET", uri="/assert/{var}")
     * @SLX\Modifier(method="assert", args={"var", "\d+"})
     */
    public function testAssertModifier($var)
    {
        return new Response($var);
    }

    /**
     * @SLX\Request(method="GET", uri="/requirehttps")
     * @SLX\Modifier("requireHttps")
     */
    public function testRequireHttpsModifier()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/host")
     * @SLX\Modifier(method="host", args="www.wronghost.com")
     */
    public function testHostModifier()
    {
        return new Response();
    }
}