<?php 

namespace DDesrosiers\SilexAnnotations\Annotations;

use DDesrosiers\Silex\AnnotationsAnnotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class After implements RouteAnnotation
{
	/** @var string */
	public $callback;
	
	public function process(\Silex\Controller $route)
	{
		$route->after($this->callback);
	}
}

