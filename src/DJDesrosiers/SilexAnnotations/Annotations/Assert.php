<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

use DJDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Assert implements RouteAnnotation
{
	/** @var string */
	public $variable;

	/** @var string */
	public $regex;
	
	public function process(\Silex\Route $route)
	{
		$route->assert($this->variable, $this->regex);
	}
}
