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
use Symfony\Component\HttpFoundation\Request;

class RequireHttpsTest extends AnnotationTestBase
{
    public function testHttps()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/requirehttps", self::STATUS_OK);
    }

    /**
     * @throws \Exception
     */
    public function testHttp()
    {
        // we make the request as http, but it should be redirected to a https request
        $this->registerProviders();
        $_SERVER['REQUEST_URI'] = '/test/requirehttps';
        $request = Request::create('https://test.com/test/requirehttps');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/test/requirehttps'));
    }

    public function testHttpsCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/requirehttps", self::STATUS_OK);
    }

    /**
     * @throws \Exception
     */
    public function testHttpCollection()
    {
        // we make the request as http, but it should be redirected to a https request
        $this->registerProviders();
        $_SERVER['REQUEST_URI'] = '/test/requirehttps';
        $request = Request::create('https://test.com/test/requirehttps');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/test/requirehttps'));
    }
}
