<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations\Annotations;

use DDesrosiers\Test\SilexAnnotations\AnnotationTestBase;
use Silex\Provider\SecurityServiceProvider;

class SecureTest extends AnnotationTestBase
{
    protected $authRequestOptions = array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'foo');

    public function setup()
    {
        parent::setup();

        $this->app->register(
                  new SecurityServiceProvider(),
                      array(
                          'security.firewalls' => array(
                              'admin' => array(
                                  'pattern' => '^/test',
                                  'http'    => true,
                                  'users'   => array(
                                      // raw password is foo
                                      'admin' => array(
                                          'ROLE_ADMIN',
                                          '$2y$15$lzUNsTegNXvZW3qtfucV0erYBcEqWVeyOmjolB7R1uodsAVJ95vvu'
                                      ),
                                  ),
                              ),
                          )
                      )
        );
    }

    public function testAuthorizedUser()
    {
        $this->requestOptions = $this->authRequestOptions;
        $this->assertEndPointStatus(self::GET_METHOD, "/test/secure", self::STATUS_OK);
    }

    public function testUnauthorizedUser()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/test/secure", self::STATUS_UNAUTHORIZED);
    }

    public function testAuthorizedUserCollection()
    {
        $this->requestOptions = $this->authRequestOptions;
        $this->assertEndPointStatus(self::GET_METHOD, "/testSecure/test", self::STATUS_OK);
    }

    public function testUnauthorizedUserCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, "/testSecure/test", self::STATUS_UNAUTHORIZED);
    }
}
