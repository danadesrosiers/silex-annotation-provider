<?php 
use DJDesrosiers\Silex\Annotations as Silex;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerAutoloadNamespace("DJDesrosiers\Silex\Annotations", __DIR__."/../src");

class TestController 
{
	/**
	 * @Silex\Route(
	 *		@Silex\Request(method="GET", pattern="test"),
	 *		@Silex\Assert(variable="test", regex="myreg"),
	 *		@Silex\Assert(variable="test", regex="myreg"),
	 *		@Silex\RequireHttp,
	 *		@Silex\Convert(variable="var1", callback="TestController::converter")
	 * )
	 */
	public function testMethod()
	{
		
	}
}

$controller_name = 'TestController';
$reader = new AnnotationReader();
$reflection_class = new ReflectionClass($controller_name);
foreach ($reflection_class->getMethods(ReflectionMethod::IS_PUBLIC) as $reflection_method)
{
	if (!$reflection_method->isStatic())
	{
		$method_annotations = $reader->getMethodAnnotations($reflection_method);
		print_r($method_annotations);
	}
}


