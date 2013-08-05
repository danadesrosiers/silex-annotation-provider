<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\Silex\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class RequireHttp implements RouteAnnotation
{
	public function process(\Silex\Route $route)
	{
		$route->requireHttp();
	}
}
