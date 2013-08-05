<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class RequireHttps implements RouteAnnotation
{
	public function process(\Silex\Controller $route)
	{
		$route->requireHttps();
	}
}

