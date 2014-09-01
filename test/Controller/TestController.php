<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/test")
 */
class TestController
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test1")
     * )
     */
    public function test1()
    {
        return new Response();
    }
} 