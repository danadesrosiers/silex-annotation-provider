<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => /convert
 *     convert => var, DDesrosiers\Test\SilexAnnotations\Controller\ConvertCollectionTestController::convert
 * )
 */
class ConvertCollectionTestController
{
    /**
     * @Route(GET /test/{var})
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