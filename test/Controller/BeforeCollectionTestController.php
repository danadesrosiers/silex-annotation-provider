<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => before
 *     before => DDesrosiers\Test\SilexAnnotations\Controller\BeforeTestController::beforeCallback
 * )
 */
class BeforeCollectionTestController
{
    /**
     * @Route(GET /test)
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
