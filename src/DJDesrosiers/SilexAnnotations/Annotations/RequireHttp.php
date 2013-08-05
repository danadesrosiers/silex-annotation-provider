<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class RequireHttp implements RouteAnnotation
{
	public function process(\Silex\Controller $route)
	{
		$route->requireHttp();
	}
}
