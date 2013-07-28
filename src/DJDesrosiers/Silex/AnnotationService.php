<?php 
namespace DJDesrosiers\Silex;

class AnnotationService
{
	public function __construct()
	{
		
	}
	
	public function registerController($controller_name)
	{
		// TODO: cache the reader
		$reader = new AnnotationReader();
		$reflection_class = new ReflectionClass($controller_name);
		foreach ($reflection_class->getMethods(ReflectionMethod::IS_PUBLIC) as $reflection_method)
		{
			if (!$reflection_method->isStatic())
			{
				$method_annotations = $reader->getMethodAnnotations($reflection_method);

				$modifiers = array();
				$endpoints = array();
				foreach ($method_annotations as $annot)
				{
					$func = strtolower(str_replace("Shopatron\\Annotations\\Silex\\", '', get_class($annot)));
					switch ($func)
					{
						case 'get':
						case 'post':
						case 'put':
						case 'delete':
						case 'match':
							$endpoints[] = array(
								'method' => $func,
								'uri' => $annot->uri
							);
							break;
						case 'assert':
							$modifiers[] = array($func, $annot->variable, $annot->regex);
							break;
						case 'value':
							$modifiers[] = array($func, $annot->variable, $annot->default);
							break;
						case 'convert':
							$modifiers[] = array($func, $annot->variable, $annot->callback);
							break;
						case 'host':
							$modifiers[] = array($func, $annot->host);
							break;
						case 'requirehttp':
							$modifiers[] = array('requireHttp');
							break;
						case 'requirehttps':
							$modifiers[] = array('requireHttps');
							break;
						case 'before':
							$modifiers[] = array($func, $annot->callback);
							break;
						case 'after':
							$modifiers[] = array($func, $annot->callback);
							break;
					}
				}	

				if (count($endpoints) > 0)
				{
					foreach ($endpoints as $endpoint)
					{
						$route = $this->{$endpoint['method']}($endpoint['uri'], "$controller_name:{$reflection_method->getName()}");
						foreach ($modifiers as $args)
						{
							$func = array_shift($args);
							call_user_func_array(array($route, $func), $args);
						}
					}
				}
			}
		}
	}
}
