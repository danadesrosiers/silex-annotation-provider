<?php 
namespace DJDesrosiers\Silex;

use Silex\Application;
use DJDesrosiers\Silex\Annotations\Route;

class AnnotationService
{
	/** @var Application */
	protected $app;
	
	/** @var AnnotationReader */
	protected $reader;
	
	public function __construct(Application $app)
	{
		$this->app = $app;
		
		// TODO: Allow configuration of caching service
		$this->reader = new CachedReader(
			new AnnotationReader(),
			new ApcCache(),
			$this->app['debug']
		);
	}
	
	public function registerController($controller_name)
	{
		$reflection_class = new ReflectionClass($controller_name);
		foreach ($reflection_class->getMethods(ReflectionMethod::IS_PUBLIC) as $reflection_method)
		{
			if (!$reflection_method->isStatic())
			{
				$method_annotations = $this->reader->getMethodAnnotations($reflection_method);
				foreach ($method_annotations as $annotation)
				{
					if ($annotation instanceof Route)
					{
						$annotation->process($this->app, "$controller_name:{$reflection_method->getName()}");
					}
				}
			}
		}
	}
}
