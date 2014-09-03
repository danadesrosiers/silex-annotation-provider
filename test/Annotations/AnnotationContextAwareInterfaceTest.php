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
use DDesrosiers\SilexAnnotations\AnnotationContextAwareInterface;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class AnnotationContextAwareInterfaceTest extends \PHPUnit_Framework_TestCase
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
                "annot.controllers" => array("DDesrosiers\\Test\\SilexAnnotations\\Annotations\\ContextAwareTestController")
            )
        );
        // replace with something we can test
        $this->app['annot.context'] = $this->app->share(
            function () {
                return array(
                    'modifier' => array('args' => array('var', 'bar')),
                    'request' => array('uri-prefix' => '/sub'),
                );
            }
        );

        $this->client = new Client($this->app, array('HTTP_HOST' => 'www.test.com'));
    }

    public function testContextAwareModifier()
    {
        // testing a request with context
        $this->client->request("GET", "/sub/assert/foo");
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());

        $this->client->request("GET", "/sub/assert/bar");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ContextAwareRequest extends SLX\Request implements AnnotationContextAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function setContext($context)
    {
        // naive use of context to modify behaviour
        $this->uri = $context['request']['uri-prefix'].$this->uri;
    }
}

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class ContextAwareModifier extends SLX\Modifier implements AnnotationContextAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function setContext($context)
    {
        // naive use of context to modify behaviour
        $this->args = $context['modifier']['args'];
    }
}

class ContextAwareTestController
{
    /**
     * @ContextAwareRequest(method="GET", uri="/assert/{var}")
     * @ContextAwareModifier(method="assert", args={"var", "foo"})
     */
    public function testContextAware($var)
    {
        return new Response($var);
    }

}
