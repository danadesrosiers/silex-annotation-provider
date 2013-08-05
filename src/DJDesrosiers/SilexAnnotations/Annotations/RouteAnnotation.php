<?php 
namespace DJDesrosiers\SilexAnnotations\Annotations;

interface RouteAnnotation
{
	public function process(\Silex\Route $route);
}
