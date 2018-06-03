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

use Silex\Application;

class AnnotationDirArrayTestBase extends AnnotationTestBase
{
    public function setup()
    {
        self::$CONTROLLER_DIR = array(
            __DIR__ . "/Controller",
            __DIR__ . "/Controller2"
        );
        $this->app = new Application();
        $this->app['debug'] = true;
    }
} 