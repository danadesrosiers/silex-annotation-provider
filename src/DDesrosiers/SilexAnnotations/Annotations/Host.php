<?php 

namespace DDesrosiers\SilexAnnotations\Annotations;

use DDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Host implements RouteAnnotation
{
	/** @var string */
	public $host;
	
	public function process(\Silex\Controller $route)
	{
		$route->host($this->host);
	}
}
