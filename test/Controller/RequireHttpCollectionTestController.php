<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => /requirehttp
 *     requireHttp
 * )
*/
class RequireHttpCollectionTestController
{
    /**
     * @Route(GET /test)
     * )
     */
    public function testRequireHttp()
    {
        return new Response();
    }
}