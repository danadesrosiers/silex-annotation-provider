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

class BeforeTest extends AnnotationTestBase
{
    public function testBefore()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/before', self::STATUS_ERROR);
    }

    public function testBeforeCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/before/test', self::STATUS_ERROR);
    }
}
