<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller
 */
class TestControllerWithNoPrefix
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     */
    public function testMethod()
    {
        return new Response();
    }
}
