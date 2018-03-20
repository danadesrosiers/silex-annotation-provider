<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/two")
 */
class TestController2
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="test")
     * )
     */
    public function test1()
    {
        return new Response();
    }
}