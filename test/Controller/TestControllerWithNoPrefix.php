<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller
 * @SLX\Before("DDesrosiers\SilexAnnotations\Test\Annotations\BeforeTestController::beforeCallback")
 */
class TestControllerWithNoPrefix
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    public static function beforeCallback()
    {
        throw new \Exception("before callback");
    }
}
