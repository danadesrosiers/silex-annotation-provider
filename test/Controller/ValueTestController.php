<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(/)
 */
class ValueTestController
{
    /**
     * @Route(
     *     uri => GET /{var}
     *     value => var, default
     * )
     *
     * @param $var
     * @return Response
     */
    public function testMethod($var)
    {
        return new Response($var);
    }
}
