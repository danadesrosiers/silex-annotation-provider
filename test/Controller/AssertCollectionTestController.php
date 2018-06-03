<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="assert")
 * @SLX\Assert(variable="var", regex="\d+")
 */
class AssertCollectionTestController
{
    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="test/{var}")
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
