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
use DDesrosiers\Test\SilexAnnotations\Controller\AfterCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\AssertCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\BeforeCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\ConvertCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\HostCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\RequireHttpCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\RequireHttpsCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\SecureCollectionTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\SubDir\SubDirTestController;
use DDesrosiers\Test\SilexAnnotations\Controller\TestController;
use DDesrosiers\Test\SilexAnnotations\Controller\TestController2;
use DDesrosiers\Test\SilexAnnotations\Controller\ValueTestController;

class AnnotationServiceProviderTest extends AnnotationTestBase
{
    public function registerControllersByDirectoryTestProvider()
    {
        $allControllers = [
            AfterCollectionTestController::class,
            AssertCollectionTestController::class,
            BeforeCollectionTestController::class,
            ConvertCollectionTestController::class,
            HostCollectionTestController::class,
            RequireHttpCollectionTestController::class,
            RequireHttpsCollectionTestController::class,
            SecureCollectionTestController::class,
            SubDirTestController::class,
            TestController::class,
            TestController2::class,
            ValueTestController::class
        ];

        return array(
            array("/SubDir", [SubDirTestController::class]),
            array('', $allControllers)
        );
    }

    /**
     * @dataProvider registerControllersByDirectoryTestProvider
     * @param $dir
     * @param $result
     */
    public function testRegisterControllersByDirectory($dir, $result)
    {
        $service = $this->registerProviders();
        $files = $service->discoverControllers(self::$CONTROLLER_DIR.$dir);
        self::assertEquals($result, $files);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testControllerCache()
    {
        $_SERVER['REQUEST_URI'] = '';
        $cacheKey = AnnotationService::CONTROLLER_CACHE_INDEX;
        $cache = new TestArrayCache();
        $this->app['annot.cache'] = $cache;
        $this->app['debug'] = false;
        $service = $this->registerProviders();
        $service->registerControllers(self::$CONTROLLER_DIR, []);
        $this->assertCount(12, $this->flattenControllerArray($cache->get($cacheKey)));

        $cache->clearWasFetched();
        $service->registerControllers(self::$CONTROLLER_DIR, []);
        $this->assertTrue($cache->wasFetched($cacheKey));
        $controllers = $cache->get($cacheKey);
        $this->assertContains(SubDirTestController::class, $controllers['/']);
        $this->assertContains(TestController::class, $controllers['/test']);
        $this->assertCount(12, $this->flattenControllerArray($controllers));
    }
}
