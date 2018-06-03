<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/convert")
 * @SLX\Convert(variable="var", callback="DDesrosiers\Test\SilexAnnotations\Controller\ConvertCollectionTestController::convert")
 */
class ConvertCollectionTestController
{
    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="/test/{var}")
     * )
     *
     * @param $var
     * @return Response
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