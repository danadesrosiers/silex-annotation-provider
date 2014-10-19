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
use Doctrine\Common\Cache\ApcCache;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

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

    public function testCacheUsingStringIdentifier()
    {
        $this->getClient(array('annot.cache' => 'Array'));

        /** @var AnnotationService $service */
        $service = $this->app['annot'];
        $this->assertInstanceOf("Doctrine\\Common\\Annotations\\CachedReader", $service->getReader());
    }

    public function testCacheUsingImplementationOfCache()
    {
        $this->getClient(array('annot.cache' => new ApcCache()));

        /** @var AnnotationService $service */
        $service = $this->app['annot'];
        $this->assertInstanceOf("Doctrine\\Common\\Annotations\\CachedReader", $service->getReader());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidCacheString()
    {
        $this->getClient(array('annot.cache' => 'Fake'));
        $this->app['annot'];
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidCacheClass()
    {
        $this->getClient(array('annot.cache' => new InvalidCache()));
        $this->app['annot'];
    }
}

class InvalidCache
{

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