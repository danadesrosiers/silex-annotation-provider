<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => /testSecure
 *     secure => ROLE_ADMIN
 * )
 */
class SecureCollectionTestController
{
    /**
     * @Route(GET /test)
     */
    public function testSecure()
    {
        return new Response();
    }
}
