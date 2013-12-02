<?php

/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 * @copyright (c) 2013, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\Annotations;

use Silex\Application;
use Silex\Controller;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Secure implements RouteAnnotation
{
    /** @var mixed */
    public $role;

    /**
     * @param Controller $route
     */
    public function process(Controller $route)
    {
        if (method_exists($route, "secure")) {
            $route->secure($this->role);
        } else {
            $roles = $this->role;
            $route->before(
                function (SymfonyRequest $request, Application $app) use ($roles) {
                    if (!$app["security"]->isGranted($roles)) {
                        throw new AccessDeniedException();
                    }
                }
            );
        }
    }
}
