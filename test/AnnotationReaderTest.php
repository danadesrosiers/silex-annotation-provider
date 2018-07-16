<?php

namespace DDesrosiers\Test\SilexAnnotations;

use DDesrosiers\SilexAnnotations\AnnotationReader;
use PHPUnit\Framework\TestCase;

class AnnotationReaderTest extends TestCase
{
    public function testParseAnnotation()
    {
        $def = (new AnnotationReader())->getControllerAnnotation(AnnotationReaderTestController::class);
        self::assertEquals('/test', $def->getPrefix());
        self::assertCount(4, $def->getModifiers());
        self::assertCount(1, $def->getRoutes());
    }
}

/**
 * @Controller(
 *     prefix => test
 *     after => \DDesrosiers\Controller\TestController::converter
 *     host => www.test.com
 *     requireHttp
 *     secure => ADMIN
 * )
 */
class AnnotationReaderTestController
{
    /**
     * @Route(
     *     uri => GET test/{var}
     *     assert => var, \d+
     *     convert => var, \DDesrosiers\Controller\TestController::converter
     *     after => \DDesrosiers\Controller\TestController::converter
     *     host => www.test.com
     *     requireHttp
     *     secure => ADMIN
     *     value => var, default
     * )
     */
    public function route()
    {

    }
}