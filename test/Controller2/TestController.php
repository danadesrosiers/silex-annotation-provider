<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Exception;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * @SLX\Controller(prefix="/test")
 */
class TestController
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test1")
     * )
     */
    public function test1()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="POST", uri="/post")
     */
    public function testPostRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="PUT", uri="/put")
     */
    public function testPutRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="DELETE", uri="/delete")
     */
    public function testDeleteRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="MATCH", uri="/match")
     */
    public function testMatchRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET|POST", uri="/multi-method")
     */
    public function testMultiMethodRequest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="firstRequest")
     * @SLX\Request(method="GET", uri="secondRequest")
     * @return Response
     */
    public function testMultipleRequests()
    {
        return new Response();
    }

    /**
     * The assert modifier should be applied to both endpoints.
     *
     * @SLX\Request(method="GET", uri="firstRequest/{num}")
     * @SLX\Request(method="GET", uri="secondRequest/{num}")
     * @SLX\Assert(variable="num", regex="\d+")
     * @return Response
     */
    public function testMultipleRequestsShareModifiers()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="assert/{var}")
     * @SLX\Assert(variable="var", regex="\d+")
     */
    public function assertTest($var)
    {
        return new Response($var);
    }

    /**
     * @SLX\Request(method="GET", uri="/before")
     * @SLX\Before("DDesrosiers\Test\SilexAnnotations\Controller\TestController::beforeCallback")
     */
    public function beforeTest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/after")
     * @SLX\After("DDesrosiers\Test\SilexAnnotations\Controller\TestController::afterCallback")
     */
    public function afterTest()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/bind")
     * @SLX\Bind(routeName="testRouteName")
     */
    public function bindTest(Application $app)
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $app['url_generator'];
        return new Response($urlGenerator->generate('testRouteName'));
    }

    /**
     * @SLX\Request(method="GET", uri="/convert/{var}")
     * @SLX\Convert(variable="var", callback="DDesrosiers\Test\SilexAnnotations\Controller\TestController::convert")
     */
    public function convertTest($var)
    {
        return new Response($var);
    }

    /**
     * @SLX\Request(method="GET", uri="/hostTest")
     * @SLX\Host("www.test.com")
     */
    public function testHost()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/requirehttp")
     * @SLX\RequireHttp
     */
    public function testRequireHttp()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/requirehttps")
     * @SLX\RequireHttp
     */
    public function testRequireHttps()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/secure")
     * @SLX\Secure("ROLE_ADMIN")
     */
    public function testSecure()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/assert/modifier/{var}")
     * @SLX\Modifier(method="assert", args={"var", "\d+"})
     */
    public function testAssertModifier($var)
    {
        return new Response($var);
    }

    /**
     * @SLX\Request(method="GET", uri="/requirehttps/modifier")
     * @SLX\Modifier("requireHttps")
     */
    public function testRequireHttpsModifier()
    {
        return new Response();
    }

    /**
     * @SLX\Request(method="GET", uri="/host/modifier")
     * @SLX\Modifier(method="host", args="www.wronghost.com")
     */
    public function testHostModifier()
    {
        return new Response();
    }

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/route/{var}"),
     *      @SLX\Assert(variable="var", regex="\d+")
     * )
     *
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/route2/{var}")
     * )
     */
    public function routeTest($var)
    {
        return new Response($var);
    }

    public static function beforeCallback()
    {
        throw new Exception("before callback");
    }

    public static function afterCallback()
    {
        throw new Exception("after callback");
    }

    public static function convert($var)
    {
        return $var + 5;
    }
} 