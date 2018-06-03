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

class AnnotationServiceDirArrayTest extends AnnotationDirArrayTestBase
{
    public function testServiceControllerDirArray()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
        $this->setup();
        $this->assertEndPointStatus(self::GET_METHOD, '/test2/test1', self::STATUS_OK);
    }

    public function testIsolationOfControllerModifiersDirArray()
    {        
        $this->assertEndPointStatus(self::GET_METHOD, '/before/test', self::STATUS_ERROR);
        $this->setup();
        $this->assertEndPointStatus(self::GET_METHOD, '/before2/test', self::STATUS_ERROR);
        $this->setup();
        $this->assertEndPointStatus(self::GET_METHOD, '/test2/test1', self::STATUS_OK);
        $this->setup();
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
    }

    public function testControllerCache()
    {
        $cache = new TestArrayCache();
        $this->app['annot.cache'] = $cache;
        $this->app['debug'] = false;

        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);

        // all controllers should be loaded and cached now
        $cacheKey = AnnotationService::CONTROLLER_CACHE_INDEX;

        // create new instance, now controllers should load from cache
        $cache->clearWasFetched();
        unset($this->app);
        $this->setup();
        $this->app['annot.cache'] = $cache;
        $this->app['debug'] = false;

        // spot check a URI from each directory
        $this->assertEndPointStatus(self::GET_METHOD, '/test/test1', self::STATUS_OK);
        $this->setup();
        $this->assertEndPointStatus(self::GET_METHOD, '/test2/test1', self::STATUS_OK);

        // check that we got the controllers from cache
        $this->assertTrue($cache->wasFetched($cacheKey));

        $controllers = $cache->get($cacheKey);
        $this->assertCount(13, $controllers);
        $this->assertCount(25, $this->flattenControllerArray($controllers));
    }
}

class InvalidCache
{

}
