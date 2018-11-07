<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => /after
 *     after => DDesrosiers\Test\SilexAnnotations\Controller\AfterTestController::afterCallback
 * )
 */
class AfterCollectionTestController
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
    public static function afterCallback()
    {
        throw new Exception("after callback");
    }
}