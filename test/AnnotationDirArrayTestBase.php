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
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class AnnotationDirArrayTestBase extends TestCase
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
        self::$CONTROLLER_DIR = array(
            __DIR__ . "/Controller",
            __DIR__ . "/Controller2"
        );
        $this->app = new Application();
        $this->app['debug'] = true;
    }

    /**
     * @param array $options
     * @return AnnotationService
     */
    protected function registerAnnotations($options = array())
    {
        if (!isset($options['annot.controllers'])) {
            $options['annot.controllerDir'] = self::$CONTROLLER_DIR;
        }

        $this->app->register(new AnnotationServiceProvider(), $options);
        return $this->app['annot'];
    }

    protected function getClient($annotationOptions = array())
    {
        if (!$this->app->offsetExists('annot')) {
            $this->registerAnnotations($annotationOptions);
        }
        $this->client = new Client($this->app, $this->clientOptions);
    }

    protected function assertEndPointStatus($method, $uri, $status, $annotationOptions = array())
    {
        $this->assertStatus($this->makeRequest($method, $uri, $annotationOptions), $status);
    }

    protected function makeRequest($method, $uri, $annotationOptions = array())
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $this->getClient($annotationOptions);
        $this->client->request($method, $uri, array(), array(), $this->requestOptions);
        $response = $this->client->getResponse();
        return $response;
    }

    protected function assertStatus(Response $response, $status)
    {
        $this->assertEquals($status, $response->getStatusCode());
    }
} 