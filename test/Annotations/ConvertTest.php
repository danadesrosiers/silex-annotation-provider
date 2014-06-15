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

class ConvertTest extends \PHPUnit_Framework_TestCase
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
                      "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\Annotations\\ConvertTestController")
                  )
        );

        $this->client = new Client($this->app);
    }

    public function testConvert()
    {
        $this->client->request("GET", "/45");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("50", $response->getContent());
    }
}

class ConvertTestController
{
    /**
     * @SLX\Request(method="GET", uri="/{var}")
     * @SLX\Convert(variable="var", callback="DDesrosiers\Test\SilexAnnotations\Annotations\ConvertTestController::convert")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    public static function convert($var)
    {
        return $var + 5;
    }
}