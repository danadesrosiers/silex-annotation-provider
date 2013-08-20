<?php 
namespace DDesrosiers\SilexAnnotations;

use DDesrosiers\SilexAnnotations\Annotations\Request;
use DDesrosiers\SilexAnnotations\Annotations\Route;
use DDesrosiers\SilexAnnotations\Annotations\RouteAnnotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Silex\Application;

class AnnotationService
{
	/** @var Application */
	protected $app;
	
	/** @var AnnotationReader */
	protected $reader;
	
	public function __construct(Application $app)
	{
		$this->app = $app;
		
		if ($app->offsetExists('annot.cache') && strlen($app['annot.cache']) > 0)
		{
			$cache_class = "Doctrine\\Common\\Cache\\{$app['annot.cache']}Cache";
			if (!class_exists($cache_class))
			{
				throw new RuntimeException("Cache type: [$cache_class] does not exist.  Make sure you include Doctrine cache.");
			}
			
			$this->reader = new CachedReader(
				new AnnotationReader(),
				new $cache_class(),
				$this->app['debug']
			);
		}
		else 
		{
			$this->reader = new AnnotationReader();
		}
	}
	
	public function process($controllerName, $isServiceController=true, $newCollection=false)
	{
		$separator = $isServiceController ? ":" : "::";
		$controllerCollection = $newCollection ? $this->app['controllers_factory'] : $this->app['controllers'];
		$reflection_class = new ReflectionClass($controllerName);
		foreach ($reflection_class->getMethods(ReflectionMethod::IS_PUBLIC) as $reflection_method)
		{
			if (!$reflection_method->isStatic())
			{
				$method_annotations = $this->reader->getMethodAnnotations($reflection_method);
				$controllerMethodName = $controllerName.$separator.$reflection_method->getName();
				foreach ($method_annotations as $annotation)
				{
					if ($annotation instanceof Route)
					{
						$annotation->process($this->app, $controllerMethodName, $controllerCollection);
					}
					else if ($annotation instanceof Request)
					{
						$controller = $annotation->process($controllerCollection, $controllerMethodName);
						foreach ($method_annotations as $routeAnnotation)
						{
							if ($routeAnnotation instanceof RouteAnnotation)
							{
								$routeAnnotation->process($controller);
							}
						}
					}
				}
			}
		}
		
		return $controllerCollection;
	}
}
