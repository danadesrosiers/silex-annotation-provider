<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Exception;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * @Controller(/test)
 */
class TestController
{
    /**
     * @Route(GET test1)
     */
    public function test1()
    {
        return new Response();
    }

    /**
     * @Route(POST /post)
     */
    public function testPostRequest()
    {
        return new Response();
    }

    /**
     * @Route(PUT /put)
     */
    public function testPutRequest()
    {
        return new Response();
    }

    /**
     * @Route(DELETE /delete)
     */
    public function testDeleteRequest()
    {
        return new Response();
    }

    /**
     * @Route(/match)
     */
    public function testMatchRequest()
    {
        return new Response();
    }

    /**
     * @Route(GET|POST /multi-method)
     */
    public function testMultiMethodRequest()
    {
        return new Response();
    }

    /**
     * @Route(
     *     uri => GET assert/{var}
     *     assert => var, \d+
     * )
     *
     * @param $var
     * @return Response
     */
    public function assertTest($var)
    {
        return new Response($var);
    }

    /**
     * @Route(
     *     uri => GET /before
     *     before => DDesrosiers\Test\SilexAnnotations\Controller\TestController::beforeCallback
     * )
     */
    public function beforeTest()
    {
        return new Response();
    }

    /**
     * @Route(
     *     uri => GET /after
     *     after => DDesrosiers\Test\SilexAnnotations\Controller\TestController::afterCallback
     * )
     */
    public function afterTest()
    {
        return new Response();
    }

    /**
     * @Route(
     *     uri => GET /bind
     *     bind => testRouteName
     * )
     *
     * @param Application $app
     * @return Response
     */
    public function bindTest(Application $app)
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $app['url_generator'];
        return new Response($urlGenerator->generate('testRouteName'));
    }

    /**
     * @Route(
     *     uri => GET /convert/{var}
     *     convert => var, DDesrosiers\Test\SilexAnnotations\Controller\TestController::convert
     * )
     *
     * @param $var
     * @return Response
     */
    public function convertTest($var)
    {
        return new Response($var);
    }

    /**
     * @Route(
     *     uri => GET /hostTest
     *     host => www.test.com
     * )
     */
    public function testHost()
    {
        return new Response();
    }

    /**
     * @Route(
     *     uri => GET /requirehttp
     *     requireHttp
     * )
     */
    public function testRequireHttp()
    {
        return new Response();
    }

    /**
     * @Route(
     *     uri => GET /requirehttps
     *     requireHttp
     * )
     */
    public function testRequireHttps()
    {
        return new Response();
    }

    /**
     * @Route(
     *     uri => GET /secure
     *     secure => ROLE_ADMIN
     * )
     */
    public function testSecure()
    {
        return new Response();
    }

    /**
     * @throws Exception
     */
    public static function beforeCallback()
    {
        throw new Exception("before callback");
    }

    /**
     * @throws Exception
     */
    public static function afterCallback()
    {
        throw new Exception("after callback");
    }

    public static function convert($var)
    {
        return $var + 5;
    }
} 