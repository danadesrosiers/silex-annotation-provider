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

class ConvertTest extends AnnotationTestBase
{
    public function testConvert()
    {
        $response = $this->makeRequest(self::GET_METHOD, '/test/convert/45');
        $this->assertStatus($response, self::STATUS_OK);
        $this->assertEquals("50", $response->getContent());
    }

    public function testConvertCollection()
    {
        $response = $this->makeRequest(self::GET_METHOD, '/convert/test/45');
        $this->assertStatus($response, self::STATUS_OK);
        $this->assertEquals("50", $response->getContent());
    }
}
