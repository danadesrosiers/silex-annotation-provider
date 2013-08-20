<?php 

namespace DDesrosiers\SilexAnnotations\Annotations;

use Silex\Application;
use DDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;

/**
 * @Annotation
 * @Target("METHOD")
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
	
	public function __construct(array $values)
	{
		foreach ($values['value'] as $annotation)
		{
			$classPath = explode("\\", get_class($annotation));
			$propertyName = lcfirst(array_pop($classPath));
			$this->{$propertyName}[] = $annotation;
		}
	}
	
	/**
	 * Process annotations on a method to register it as a controller.
	 * 
	 * @param \Silex\Application $app
	 * @param string $controllerName fully qualified method name of the controller
	 * @param boolean $newControllerCollection if true add any controllers to a new controller collection,
	 *											else add to default controller collection
	 * @return ControllerCollection the controller collection that holds the added controllers
	 */
	public function process(Application $app, $controllerName, $controllerCollection)
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
		return $controllerCollection;
	}
}