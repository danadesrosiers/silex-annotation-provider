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

use DDesrosiers\SilexAnnotations\AnnotationService;
use Symfony\Component\HttpFoundation\Response;

include __DIR__ . "/NoNamespace/TestControllerNoNamespace.php";

class AnnotationServiceProviderTest extends AnnotationTestBase
{
    public function registerControllersByDirectoryTestProvider()
    {
        $subDirFqcn = self::CONTROLLER_NAMESPACE."SubDir\\SubDirTestController";
        return array(
            array("/SubDir", $subDirFqcn),
            array("/SubDir", $subDirFqcn),
            array('', $subDirFqcn),
            array("/../NoNamespace", "TestControllerNoNamespace")
        );
    }

    /**
     * @dataProvider registerControllersByDirectoryTestProvider
     */
    public function testRegisterControllersByDirectory($dir, $result)
    {
        $service = $this->registerAnnotations();
        $files = $service->discoverControllers([self::$CONTROLLER_DIR.$dir]);
        if (is_array($result)) {
            $this->assertEquals($result, $files);
        } else {
            $this->assertContains($result, $files['/']);
        }
    }

    public function testControllerCache()
    {
        $cacheKey = AnnotationService::CONTROLLER_CACHE_INDEX;
        $cache = new TestArrayCache();
        $this->app['annot.cache'] = $cache;
        $this->app['debug'] = false;
        $service = $this->registerAnnotations();
        $service->discoverControllers([self::$CONTROLLER_DIR]);
        $this->assertCount(13, $this->flattenControllerArray($cache->get($cacheKey)));

        $files = $service->discoverControllers([self::$CONTROLLER_DIR]);
        $flatControllers = $this->flattenControllerArray($files);
        $this->assertTrue($cache->wasFetched($cacheKey));
        $this->assertContains(self::CONTROLLER_NAMESPACE."SubDir\\SubDirTestController", $flatControllers);
        $this->assertContains(self::CONTROLLER_NAMESPACE."TestController", $files['/test']);
        $this->assertCount(13, $flatControllers);
    }
}

class TestControllerOne
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/test1")
     * )
     */
    public function test()
    {
        return new Response();
    }
}

class TestControllerTwo
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/test2")
     * )
     */
    public function test()
    {
        return new Response();
    }
}