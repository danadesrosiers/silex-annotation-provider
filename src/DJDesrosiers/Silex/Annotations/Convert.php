<?php 

namespace DJDesrosiers\Silex\Annotations;

use DJDesrosiers\Silex\Annotations\RouteAnnotation;

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
	
	public function process(\Silex\Route $route)
	{
		$route->convert($this->variable, $this->callback);
	}
}
