<?php 

namespace DDesrosiers\SilexAnnotations\Annotations;

use DDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

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

