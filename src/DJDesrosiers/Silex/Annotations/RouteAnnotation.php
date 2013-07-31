<?php 
namespace DJDesrosiers\Silex\Annotations;

interface RouteAnnotation
{
	public function process(\Silex\Route $route);
}
