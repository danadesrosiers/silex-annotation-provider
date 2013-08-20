<?php 

namespace DDesrosiers\SilexAnnotations\Annotations;

use Silex\ControllerCollection;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Request
{
	/** @var string */
	public $method;
	
	/** @var string */
	public $uri;
	
	/**
	 * Register the method $controllerName as controller for the given method and uri.
	 * 
	 * @param \Silex\ControllerCollection $cc
	 * @param string $controllerName Fully qualified method name of the controller
	 * @return \Silex\Controller
	 */
	public function process(ControllerCollection $cc, $controllerName)
	{
		return $cc->{$this->method}($this->uri, $controllerName);
	}
}
