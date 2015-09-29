<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller2;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/convert")
 * @SLX\Convert(variable="var", callback="DDesrosiers\Test\SilexAnnotations\Controller\ConvertCollectionTestController::convert")
 */
class ConvertCollectionTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test/{var}")
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