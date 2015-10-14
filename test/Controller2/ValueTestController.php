<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller2;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/")
 */
class ValueTestController
{
    /**
     * @SLX\Request(method="GET", uri="/{var}")
     * @SLX\Value(variable="var", default="default")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }
}
