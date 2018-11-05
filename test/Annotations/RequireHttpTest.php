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

class RequireHttpTest extends AnnotationTestBase
{
    public function testHttp()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/requirehttp", self::STATUS_OK);
    }

    /**
     * @throws \Exception
     */
    public function testHttps()
    {
        // we make the request as https, but it should be redirected to a http request
        $this->registerProviders();
        $_SERVER['REQUEST_URI'] = '/test/requirehttp';
        $request = Request::create('https://test.com/test/requirehttp');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/test/requirehttp'));
    }

    public function testHttpCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/requirehttp/test", self::STATUS_OK);
    }

    /**
     * @throws \Exception
     */
    public function testHttpsCollection()
    {
        // we make the request as https, but it should be redirected to a http request
        $this->registerProviders();
        $_SERVER['REQUEST_URI'] = '/requirehttp/test';
        $request = Request::create('https://test.com/requirehttp/test');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/requirehttp/test'));
    }
}
