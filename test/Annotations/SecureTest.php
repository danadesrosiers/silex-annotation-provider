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

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class SecureTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    protected $app;

    /** @var Client */
    protected $client;

    public function setUp()
    {
        $this->app = new Application();
        $this->app['debug'] = true;

        $this->app->register(
                  new AnnotationServiceProvider(),
                  array(
                      "annot.controllers" => array(
                          "DDesrosiers\\Test\\SilexAnnotations\\Annotations\\SecureTestController",
                          "DDesrosiers\\Test\\SilexAnnotations\\Annotations\\SecureCollectionTestController"
                      )
                  )
        );

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
                                      '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='
                                  ),
                              ),
                          ),
                      )
                  )
        );

        $this->client = new Client($this->app);
    }

    public function testAuthorizedUser()
    {
        $this->client->request(
                     "GET",
                     "/test",
                     array(),
                     array(),
                     array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'foo')
        );
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testUnauthorizedUser()
    {
        $this->client->request("GET", "/test");
        $response = $this->client->getResponse();
        $this->assertEquals('401', $response->getStatusCode());
    }

    public function testAuthorizedUserCollection()
    {
        $this->client->request(
                     "GET",
                         "/test/test",
                         array(),
                         array(),
                         array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'foo')
        );
        $response = $this->client->getResponse();
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testUnauthorizedUserCollection()
    {
        $this->client->request("GET", "/test/test");
        $response = $this->client->getResponse();
        $this->assertEquals('401', $response->getStatusCode());
    }
}

class SecureTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     * @SLX\Secure("ROLE_ADMIN")
     */
    public function testSecure()
    {
        return new Response();
    }
}

/**
 * @SLX\Controller(prefix="test")
 * @SLX\Secure("ROLE_ADMIN")
 */
class SecureCollectionTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     */
    public function testSecure()
    {
        return new Response();
    }
}
