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

use Silex\ControllerCollection;
use DDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target("METHOD")
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class Route
{
	/** @var array<Request> */
	public $request;
	
	/** @var array<Convert> */
	public $convert;
	
	/** @var array<Assert> */
	public $assert;
	
	/** @var array<RequireHttp> */
	public $requireHttp;
	
	/** @var array<RequireHttps> */
	public $requireHttps;
	
	/** @var array<Value> */
	public $value;
	
	/** @var array<Host> */
	public $host;
	
	/** @var array<Before> */
	public $before;
	
	/** @var array<After> */
	public $after;

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		$annotations = is_array($values['value']) ? $values['value'] : array($values['value']);
		foreach ($annotations as $annotation)
		{
			$classPath = explode("\\", get_class($annotation));
			$propertyName = lcfirst(array_pop($classPath));
			$this->{$propertyName}[] = $annotation;
		}
	}
	
	/**
	 * Process annotations on a method to register it as a controller.
	 * 
	 * @param \Silex\ControllerCollection $controllerCollection the controller collection to add the route to
	 * @param type $controllerName fully qualified method name of the controller
	 */
	public function process(\Silex\ControllerCollection $controllerCollection, $controllerName)
	{
		foreach ($this->request as $request)
		{
			$controller = $request->process($controllerCollection, $controllerName);
			foreach ($this as $annotations)
			{
				if (is_array($annotations))
				{
					foreach ($annotations as $annotation)
					{
						if ($annotation instanceof RouteAnnotation)
						{
							$annotation->process($controller);
						}
					}
				}
			}
		}
	}
}