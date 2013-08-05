<?php 

namespace DJDesrosiers\SilexAnnotations\Annotations;

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
	
	public function process(Application $app, $callback)
	{
		foreach ($this->request as $request)
		{
			$route = $request->process($app, $callback);
			foreach ($this as $annotations)
			{
				foreach ($annotations as $annotation)
				{
					if ($annotation instanceof RouteAnnotation)
					{
						$annotation->process($route);
					}
				}
			}
		}
	}
}