<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Host implements RouteAnnotation
{
	/** @var string */
	public $host;
	
	public function process(\Silex\Route $route)
	{
		$route->host($this->host);
	}
}
