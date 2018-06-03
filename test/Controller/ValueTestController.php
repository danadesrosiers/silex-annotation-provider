<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/")
 */
class ValueTestController
{
    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="/{var}"),
     *     @SLX\Value(variable="var", default="default")
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
