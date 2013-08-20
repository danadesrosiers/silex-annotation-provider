<?php 
namespace DDesrosiers\SilexAnnotations\Annotations;

interface RouteAnnotation
{
	public function process(\Silex\Controller $route);
}
