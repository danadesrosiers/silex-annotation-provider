<?php 

namespace DJDesrosiers\Silex\Annotations;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Request
{
	/** @var string */
	public $method;
	
	/** @var string */
	public $pattern;
	
	public function process(Application $app, $callback)
	{
		return $app->{$this->method}($this->pattern, $callback);
	}
}
