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
use Symfony\Component\HttpFoundation\Request;

class ModifierTest extends AnnotationTestBase
{
    public function testHostOneArg()
    {
        // testing a modifier that has one argument
        $this->assertEndPointStatus(self::GET_METHOD, "/test/host/modifier", self::STATUS_NOT_FOUND);
    }

    public function testAssertMultipleArgs()
    {
        // testing a modifier that has more than one argument
        $this->assertEndPointStatus(self::GET_METHOD, "/test/assert/fail", self::STATUS_NOT_FOUND);
    }

    public function testHttpsNoArgs()
    {
        // testing a modifier that has no arguments
        // we make the request as http, but it should be redirected to a Http request
        $this->registerAnnotations();
        $_SERVER['REQUEST_URI'] = '/test/requirehttps/modifier';
        $request = Request::create('http://test.com/test/requirehttps/modifier');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://test.com/test/requirehttps/modifier'));
    }
}
