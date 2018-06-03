<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/requirehttp")
 * @SLX\RequireHttp
*/
class RequireHttpCollectionTestController
{
    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="/test")
     * )
     */
    public function testRequireHttp()
    {
        return new Response();
    }
}