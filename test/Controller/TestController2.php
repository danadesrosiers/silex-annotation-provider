<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(/two)
 */
class TestController2
{
    /**
     * @Route(GET test)
     */
    public function test1()
    {
        return new Response();
    }
}
