<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => hostTest
 *     host => www.test.com
 * )
 */
class HostCollectionTestController
{
    /**
     * @Route(GET test)
     */
    public function testHost()
    {
        return new Response();
    }
} 