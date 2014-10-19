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

use DDesrosiers\Test\SilexAnnotations\AnnotationTestBase;
use Silex\Route;
use Symfony\Component\Routing\RouteCollection;

class RequestTest extends AnnotationTestBase
{
    public function testPost()
    {
        $this->assertEndPointStatus(self::POST_METHOD, "/test/post", self::STATUS_OK);
    }

    public function testPut()
    {
        $this->assertEndPointStatus(self::PUT_METHOD, "/test/put", self::STATUS_OK);
    }

    public function testDelete()
    {
        $this->assertEndPointStatus(self::DELETE_METHOD, "/test/delete", self::STATUS_OK);
    }

    public function testMultiMethod()
    {
        $this->client->request("GET", "/multi-method");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());

        $this->client->request("POST", "/multi-method");
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testMatch()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/match", self::STATUS_OK);
        $this->assertEndPointStatus(self::POST_METHOD, "/test/match", self::STATUS_OK);
    }

    public function testMultipleRequests()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/firstRequest", self::STATUS_OK);
        $this->assertEndPointStatus(self::GET_METHOD, "/test/secondRequest", self::STATUS_OK);
    }

    public function testMultipleRequestsShareModifiers()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/firstRequest/foo", self::STATUS_NOT_FOUND);
        $this->assertEndPointStatus(self::GET_METHOD, "/test/secondRequest/foo", self::STATUS_NOT_FOUND);

        /** @var RouteCollection $routes */
        $routes = $this->app['routes'];
        /** @var Route[] $iterator */
        $iterator = $routes->getIterator();

        $firstRoute = null;
        $secondRoute = null;
        foreach ($iterator as $route) {
            if ($route->getPath() == "/test/firstRequest/{num}") {
                $firstRoute = $route;
            } else if ($route->getPath() == "/test/secondRequest/{num}") {
                $secondRoute = $route;
            }
        }

        $this->assertInstanceOf('Silex\Route', $firstRoute);
        $this->assertEquals('\d+', $firstRoute->getRequirement('num'));
        $this->assertInstanceOf('Silex\Route', $secondRoute);
        $this->assertEquals('\d+', $secondRoute->getRequirement('num'));
    }
}
