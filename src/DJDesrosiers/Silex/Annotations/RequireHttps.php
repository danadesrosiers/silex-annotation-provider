<?php 

namespace DJDesrosiers\Silex\Annotations;

use DJDesrosiers\Silex\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class RequireHttps implements RouteAnnotation
{
	public function process(\Silex\Route $route)
	{
		$route->requireHttps();
	}
}

