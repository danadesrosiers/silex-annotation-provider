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
use Silex\Provider\UrlGeneratorServiceProvider;

class BindTest extends AnnotationTestBase
{
    public function testBind()
    {
        $this->app->register(new UrlGeneratorServiceProvider());
        $response = $this->makeRequest(self::GET_METHOD, '/test/bind');
        $this->assertEquals('/test/bind', $response->getContent());
    }
}

