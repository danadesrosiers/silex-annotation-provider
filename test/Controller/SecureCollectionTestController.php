<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/testSecure")
 * @SLX\Secure("ROLE_ADMIN")
 */
class SecureCollectionTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     */
    public function testSecure()
    {
        return new Response();
    }
}
