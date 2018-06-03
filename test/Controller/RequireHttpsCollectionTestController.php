<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/requirehttps")
 * @SLX\RequireHttps
*/
class RequireHttpsCollectionTestController
{
    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="/test")
     * )
     */
    public function testRequiresHttp()
    {
        return new Response();
    }
}