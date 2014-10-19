<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations\Annotations;

use DDesrosiers\Test\SilexAnnotations\AnnotationTestBase;

class RouteTest extends AnnotationTestBase
{
    public function testMultipleRoutes()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/route/45", self::STATUS_OK);

        $this->assertEndPointStatus(self::GET_METHOD, "/test/route2/45", self::STATUS_OK);
    }

    public function testIsolationOfModifiers()
    {
        // The assert should not be applied to the route2 uri, so a string for {var} should match the route.
        $this->assertEndPointStatus(self::GET_METHOD, "/test/route2/string", self::STATUS_OK);
    }
}
