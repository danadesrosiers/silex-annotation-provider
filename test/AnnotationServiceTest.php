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

class AnnotationServiceTest extends AnnotationTestBase
{
    public function testServiceController()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
    }

    public function testIsolationOfControllerModifiers()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/before', self::STATUS_ERROR);
        $this->setup();
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
    }

    public function testFastRegister()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/two/test', self::STATUS_OK);
        // there are 35 routes, but only 2 are registered (the ones with prefix '/' and '/two')
        $this->assertEquals(2, count($this->app['routes']->all()));
    }
}

class NotCache
{

}