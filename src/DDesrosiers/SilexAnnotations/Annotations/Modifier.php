<?php 
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 * @copyright (c) 2013, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\Annotations;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Modifier implements RouteAnnotation
{
	/** @var string */
	public $method;

	/** @var array */
	public $args;

	/**
	 * @param \Silex\Controller $controller
	 *
	 * @throws \RuntimeException
	 */
	public function process(\Silex\Controller $controller)
	{
		// check that the method exists in the Controller class or the Route class.
		if (!method_exists($controller->getRoute(), $this->method) && !method_exists($controller, $this->method))
		{
			throw new \RuntimeException("Modifier: [$this->method] does not exist.");
		}
		call_user_func_array(array($controller, $this->method), $this->args ?: array());
	}
}

