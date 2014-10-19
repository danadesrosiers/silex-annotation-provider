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

class ValueTest extends AnnotationTestBase
{
    public function testDefaultValue()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/foo", self::STATUS_OK);

        $response = $this->makeRequest(self::GET_METHOD, "/");
        $this->assertStatus($response, self::STATUS_OK);
        $this->assertEquals('default', $response->getContent());
    }
}
