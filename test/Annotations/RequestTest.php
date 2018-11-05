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

class RequestTest extends AnnotationTestBase
{
    public function requestTestDataProvider()
    {
        return array(
            array(self::POST_METHOD, "/test/post", self::STATUS_OK),
            array(self::PUT_METHOD, "/test/put", self::STATUS_OK),
            array(self::DELETE_METHOD, "/test/delete", self::STATUS_OK),
            // match tests
            array(self::GET_METHOD, "/test/multi-method", self::STATUS_OK),
            array(self::POST_METHOD, "/test/multi-method", self::STATUS_OK),
            array(self::GET_METHOD, "/test/match", self::STATUS_OK),
            array(self::POST_METHOD, "/test/match", self::STATUS_OK),
        );
    }

    /**
     * @dataProvider requestTestDataProvider
     * @param     $method
     * @param     $uri
     * @param int $status
     */
    public function testRequests($method, $uri, $status=self::STATUS_OK)
    {
        $this->assertEndPointStatus($method, $uri, $status);
    }
}
