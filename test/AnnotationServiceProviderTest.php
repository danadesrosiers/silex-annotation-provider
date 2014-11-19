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

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationService;
use Doctrine\Common\Cache\ArrayCache;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

include __DIR__ . "/NoNamespace/TestControllerNoNamespace.php";

class AnnotationServiceProviderTest extends AnnotationTestBase
{
    public function testRegisterControllersWithGroups()
    {
        $options = array(
            "annot.controllers" => array(
                'group1' => array("DDesrosiers\\Test\\SilexAnnotations\\TestControllerOne"),
                'group2' => array("DDesrosiers\\Test\\SilexAnnotations\\TestControllerTwo")
            )
        );

        $this->assertEndPointStatus(self::GET_METHOD, '/group1/test1', self::STATUS_OK, $options);
        $this->assertEndPointStatus(self::GET_METHOD, '/group2/test2', self::STATUS_OK, $options);
    }

    public function testRegisterControllersByDirectoryProvider()
    {
        $subDirFqcn = self::CONTROLLER_NAMESPACE."SubDir\\SubDirTestController";
        return array(
            array("/SubDir", self::CONTROLLER_NAMESPACE."SubDir\\", $subDirFqcn),
            array("/SubDir", null, $subDirFqcn),
            array('', null, $subDirFqcn),
            array("/../NoNamespace", null, "TestControllerNoNamespace")
        );
    }

    /**
     * @dataProvider testRegisterControllersByDirectoryProvider
     */
    public function testRegisterControllersByDirectory($dir, $namespace, $result)
    {
        $service = $this->registerAnnotations();
        $this->app['annot.controllerNamespace'] = $namespace;
        $files = $service->discoverControllers(self::$CONTROLLER_DIR.$dir);
        if (is_array($result)) {
            $this->assertEquals($result, $files);
        } else {
            $this->assertContains($result, $files);
        }
    }

    public function testCustomControllerFinder()
    {
        $service = $this->registerAnnotations();
        $this->app['annot.controllerFinder'] = $this->app->protect(function (Application $app, $dir) {
            $regex = '/^.+\CollectionTestController.php$/i';
            $iterator = new \RegexIterator(new \RecursiveDirectoryIterator($dir), $regex, \RecursiveRegexIterator::GET_MATCH);
            $files = array();
            foreach ($iterator as $filePath => $file) {
                $pathInfo = pathinfo($filePath);
                $files[] = self::CONTROLLER_NAMESPACE.$pathInfo['filename'];
            }

            return $files;
        });
        $files = $service->discoverControllers(self::$CONTROLLER_DIR);
        $this->assertCount(8, $files);
        $this->assertContains(self::CONTROLLER_NAMESPACE."AssertCollectionTestController", $files);
    }

    public function testControllerCache()
    {
        $cache = new TestArrayCache();
        $this->app['annot.cache'] = $cache;
        $this->app['debug'] = false;
        $service = $this->registerAnnotations();
        $service->discoverControllers(self::$CONTROLLER_DIR);
        $this->assertCount(13, $cache->fetch(AnnotationService::CONTROLLER_CACHE_INDEX));

        $files = $service->discoverControllers(self::$CONTROLLER_DIR);
        $this->assertTrue($cache->wasFetched(AnnotationService::CONTROLLER_CACHE_INDEX));
        $this->assertContains(self::CONTROLLER_NAMESPACE."SubDir\\SubDirTestController", $files);
        $this->assertContains(self::CONTROLLER_NAMESPACE."TestController", $files);
        $this->assertCount(13, $files);
    }
}

class TestArrayCache extends ArrayCache
{
    protected $fetchedIDs;

    public function wasFetched($id)
    {
        return isset($this->fetchedIDs[$id]);
    }

    public function fetch($id)
    {
        $this->fetchedIDs[$id] = true;
        return parent::fetch($id);
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