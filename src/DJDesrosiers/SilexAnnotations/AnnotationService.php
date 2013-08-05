<?php 
namespace DJDesrosiers\SilexAnnotations;

use Silex\Application;
use DJDesrosiers\SilexAnnotations\Annotations\Route;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ApcCache;

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
//		$this->reader = new CachedReader(
//			new AnnotationReader(),
//			new ApcCache(),
//			$this->app['debug']
//		);
		$this->reader = new AnnotationReader();
	}
	
	public function registerController($controller_name)
	{
		$reflection_class = new \ReflectionClass($controller_name);
		foreach ($reflection_class->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflection_method)
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
