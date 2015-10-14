<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller2;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="hostTest")
 * @SLX\Host("www.test.com")
 */
class HostCollectionTestController
{
    /**
     * @SLX\Request(method="GET", uri="test")
     */
    public function testHost()
    {
        return new Response();
    }
} 