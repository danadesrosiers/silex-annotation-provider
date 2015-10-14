<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller2;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/requirehttp")
 * @SLX\RequireHttp
*/
class RequireHttpCollectionTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     */
    public function testRequireHttp()
    {
        return new Response();
    }
}