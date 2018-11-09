<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\Test\SilexAnnotations;

use DDesrosiers\SilexAnnotations\AnnotationService;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Route;
use Silex\Route\SecurityTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class AnnotationTestBase extends TestCase
{
    const GET_METHOD = 'GET';
    const POST_METHOD = 'POST';
    const PUT_METHOD = 'PUT';
    const DELETE_METHOD = 'DELETE';

    const STATUS_OK = 200;
    const STATUS_REDIRECT = 301;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_NOT_FOUND = 404;
    const STATUS_ERROR = 500;

    protected static $CONTROLLER_DIR;

    /** @var Application */
    protected $app;

    /** @var Client */
    protected $client;

    protected $clientOptions = array();
    protected $requestOptions = array();

    public function setup()
    {
        self::$CONTROLLER_DIR = __DIR__ . "/Controller";
        $this->app = new Application();
        $this->app['debug'] = true;
    }

    /**
     * @param array $options
     * @return AnnotationService
     */
    protected function registerProviders($options = []): AnnotationService
    {
        if (!isset($options['annot.controllers'])) {
            $options['annot.controllerDir'] = self::$CONTROLLER_DIR;
        }

        $this->app->register(new AnnotationServiceProvider(), $options);
        $this->app['route_class'] = SecurityRoute::class;

        return $this->app['annot'];
    }

    protected function getClient($annotationOptions = array())
    {
        if (!$this->app->offsetExists('annot')) {
            $this->registerProviders($annotationOptions);
        }
        $this->client = new Client($this->app, $this->clientOptions);
    }

    /**
     * @param       $method
     * @param       $uri
     * @param       $status
     * @param array $annotationOptions
     */
    protected function assertEndPointStatus($method, $uri, $status, $annotationOptions = array())
    {
        $this->assertStatus($this->makeRequest($method, $uri, $annotationOptions), $status);
    }

    /**
     * @param       $method
     * @param       $uri
     * @param array $annotationOptions
     * @return null|Response
     */
    protected function makeRequest($method, $uri, $annotationOptions = array()): ?Response
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $this->getClient($annotationOptions);
        $this->client->request($method, $uri, array(), array(), $this->requestOptions);
        $response = $this->client->getResponse();
        return $response;
    }

    /**
     * @param Response $response
     * @param          $status
     */
    protected function assertStatus(Response $response, $status)
    {
        $this->assertEquals($status, $response->getStatusCode());
    }

    /**
     * @param $controllers
     * @return string[]
     */
    protected function flattenControllerArray($controllers): array
    {
        array_walk_recursive($controllers, function($a) use (&$flattened) { $flattened[] = $a; });

        return $flattened;
    }
}

class SecurityRoute extends Route
{
    use SecurityTrait;
}