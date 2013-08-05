<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Convert implements RouteAnnotation
{
	/** @var string */
	public $variable;

	/** @var string */
	public $callback;
	
	public function process(\Silex\Controller $route)
	{
		$route->convert($this->variable, $this->callback);
	}
}
