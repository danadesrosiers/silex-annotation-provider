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

class BindTest extends AnnotationTestBase
{
    public function testBind()
    {
        $response = $this->makeRequest(self::GET_METHOD, '/test/bind');
        $this->assertEquals('/test/bind', $response->getContent());
    }
}

