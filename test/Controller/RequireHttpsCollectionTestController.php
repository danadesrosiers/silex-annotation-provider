<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => /requirehttps
 *     requireHttps
 * )
*/
class RequireHttpsCollectionTestController
{
    /**
     * @Route(GET /test)
     */
    public function testRequiresHttp()
    {
        return new Response();
    }
}