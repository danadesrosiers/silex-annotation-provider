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

class HostTest extends AnnotationTestBase
{
    public function testCorrectHost()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.test.com');
        $this->assertEndPointStatus(self::GET_METHOD, "/test/hostTest", self::STATUS_OK);
    }

    public function testWrongHost()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.wrong.com');
        $this->assertEndPointStatus(self::GET_METHOD, "/test/hostTest", self::STATUS_NOT_FOUND);
    }

    public function testCorrectHostCollection()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.test.com');
        $this->assertEndPointStatus(self::GET_METHOD, "/hostTest/test", self::STATUS_OK);
    }

    public function testWrongHostCollection()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.wrong.com');
        $this->assertEndPointStatus(self::GET_METHOD, "/hostTest/test", self::STATUS_NOT_FOUND);
    }
}
