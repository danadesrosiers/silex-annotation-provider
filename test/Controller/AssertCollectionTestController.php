<?php

namespace DDesrosiers\Test\SilexAnnotations\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => assert
 *     assert => var, \d+
 * )
 */
class AssertCollectionTestController
{
    /**
     * @Route(GET test/{var})
     *
     * @param $var
     * @return Response
     */
    public function testMethod($var)
    {
        return new Response($var);
    }
}
