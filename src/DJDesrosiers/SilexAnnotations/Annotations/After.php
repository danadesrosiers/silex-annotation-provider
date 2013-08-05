<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\Silex\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class After implements RouteAnnotation
{
	/** @var string */
	public $callback;
	
	public function process(\Silex\Route $route)
	{
		$route->after($this->callback);
	}
}

