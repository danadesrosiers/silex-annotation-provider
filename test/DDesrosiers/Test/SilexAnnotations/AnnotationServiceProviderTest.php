<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class AnnotationServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    protected $app;

    /** @var Client */
    protected $client;

    public function testRegisterControllers()
    {
        $this->app = new Application();
        $this->app['debug'] = true;

        $this->app->register(new AnnotationServiceProvider(), array(
            "annot.srcDir" => __DIR__."/../../../../src",
            "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\TestControllerOne")
        ));

        $this->client = new Client($this->app);

        $this->client->request("GET", "/test1");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testRegisterControllersWithGroups()
    {
        $this->app = new Application();
        $this->app['debug'] = true;

        $this->app->register(new AnnotationServiceProvider(), array(
            "annot.srcDir" => __DIR__."/../../../../src",
            "annot.controllers" => array(
                'group1' => array("DDesrosiers\\Test\\SilexAnnotations\\TestControllerOne"),
                'group2' => array("DDesrosiers\\Test\\SilexAnnotations\\TestControllerTwo")
            )
        ));

        $this->client = new Client($this->app);

        $this->client->request("GET", "/group1/test1");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());

        $this->client->request("GET", "/group2/test2");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }
}

class TestControllerOne
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/test1")
     * )
     */
    public function test()
    {
        return new Response();
    }
}

class TestControllerTwo
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/test2")
     * )
     */
    public function test()
    {
        return new Response();
    }
}