<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations\Annotations;

use DDesrosiers\Test\SilexAnnotations\AnnotationTestBase;

class AssertTest extends AnnotationTestBase
{
    public function testAssert()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/assert/45', self::STATUS_OK);
        $this->assertEndPointStatus(self::GET_METHOD, '/test/assert/fail', self::STATUS_NOT_FOUND);
    }

    public function testAssertCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/assert/test/45', self::STATUS_OK);
        $this->assertEndPointStatus(self::GET_METHOD, '/assert/test/fail', self::STATUS_NOT_FOUND);
    }
}
