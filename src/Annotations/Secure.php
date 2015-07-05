<?php

/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\Annotations;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Secure implements RouteAnnotation
{
    /** @var mixed */
    public $role;

    /**
     * @inheritdoc
     */
    public function process($route)
    {
        if (method_exists($route, "secure")) {
            $route->secure($this->role);
        } else {
            $roles = $this->role;
            /** @noinspection PhpUnusedParameterInspection */
            $route->before(
                  function (SymfonyRequest $request, Application $app) use ($roles) {
                      /** @var AuthorizationChecker $security */
                      $security = $app['security.authorization_checker'];
                      if (!$security->isGranted($roles)) {
                          throw new AccessDeniedException();
                      }
                  }
            );
        }
    }
}
