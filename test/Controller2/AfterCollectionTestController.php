<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller2;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/after")
 * @SLX\After("DDesrosiers\Test\SilexAnnotations\Controller\AfterTestController::afterCallback")
 */
class AfterCollectionTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    public static function afterCallback()
    {
        throw new Exception("after callback");
    }
}