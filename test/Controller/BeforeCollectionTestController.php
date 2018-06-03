<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="before")
 * @SLX\Before("DDesrosiers\Test\SilexAnnotations\Controller\BeforeTestController::beforeCallback")
 */
class BeforeCollectionTestController
{
    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="/test")
     * )
     *
     * @param $var
     * @return Response
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    /**
     * @throws Exception
     */
    public static function beforeCallback()
    {
        throw new Exception("before callback");
    }
}
