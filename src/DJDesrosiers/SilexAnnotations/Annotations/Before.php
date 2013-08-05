<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Before implements RouteAnnotation
{
	/** @var string */
	public $callback;
	
	public function process(\Silex\Route $route)
	{
		$route->before($this->callback);
	}
}
