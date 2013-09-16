<?php 
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 * @copyright (c) 2013, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations\Annotations;

use DDesrosiers\Silex\AnnotationsAnnotations\RouteAnnotation;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class After implements RouteAnnotation
{
	/** @var string */
	public $callback;
	
	/**
	 * @param \Silex\Controller $route
	 */
	public function process(\Silex\Controller $route)
	{
		$route->after($this->callback);
	}
}

