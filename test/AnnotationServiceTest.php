<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations;

use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use DDesrosiers\Test\SilexAnnotations\Controller\TestControllerProvider;

class AnnotationServiceTest extends AnnotationTestBase
{
    public function testServiceController()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
    }

    public function testIsolationOfControllerModifiers()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test', self::STATUS_ERROR);
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
    }

    public function testControllerProvider()
    {
        $this->app->register(new AnnotationServiceProvider());
        $this->app->mount('/cp', new TestControllerProvider());

        $this->assertEndPointStatus(self::GET_METHOD, '/cp/test', self::STATUS_OK);
    }
}
