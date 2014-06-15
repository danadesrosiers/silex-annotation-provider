[![Build Status](https://travis-ci.org/danadesrosiers/silex-annotation-provider.svg?branch=master)](https://travis-ci.org/danadesrosiers/silex-annotation-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/danadesrosiers/silex-annotation-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/danadesrosiers/silex-annotation-provider/?branch=master)

silex-annotation-provider
=========================

A Silex ServiceProvider that defines annotations that can be used in a Silex controller.  Define your controllers in a class and use annotations to setup routes and define modifiers.


Installation
============

Install the silex-annotation-provider using composer. 

```
{
    "require": {
        "ddesrosiers/silex-annotation-provider": "dev-master"
    }
}
```

Registration
============
```php
$app->register(new DDesrosiers\SilexAnnotations\SilexAnnotationProvider(), array(
    "annot.cache" => new ApcCache(),
    "annot.controllers" => array("MyControllerNamespace\\MyController")
));
```

Parameters
==========
annot.cache
-----------
An instance of a class that implements Doctrine\Common\Cache\Cache.  This is the cache that will be used by the AnnotationReader to cache annotations so they don't have to be parsed every time.  Make sure to include Doctrine Cache as it is not a required dependency of this project.

annot.controllers
-----------------
An array of fully qualified controller names.  If set, the provider will automatically register each controller as a ServiceController and set up routes and modifiers based on annotations found.  Controllers can be grouped into controller collections by grouping them with an associative array using the array key as the mount point.
```php
$app['annot.controllers'] = array(
	'group1' => array(
		"MyControllerNamespace\\Controller1",
		"MyControllerNamespace\\Controller2"
	),
	'group2' => array(
		"MyControllerNamespace\\Controller3",
		"MyControllerNamespace\\Controller4"
	)
);
```

Annotate Controllers
====================
Create your controller.  The following is an example demonstrating the use of annotations to register an endpoint.
```
namespace DDesrosiers\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

class TestController 
{
	/**
	 * @SLX\Route(
	 *		@SLX\Request(method="GET", uri="test/{var}"),
	 *		@SLX\Assert(variable="var", regex="\d+"),
	 *		@SLX\RequireHttp,
	 *		@SLX\Convert(variable="var", callback="\DDesrosiers\Controller\TestController::converter")
	 * )
	 */
	public function testMethod($var)
	{
		return new Response("test Method: $var");
	}
	
	public static function converter($var)
	{
		return $var;
	}
}
```

The annotations in our TestController are interpreted as follows:
```
$app->get("test/{var}", "\\DDesrosiers\\Controller\\TestController:testMethod")
	->assert('var', '\d+')
	->requireHttp()
	->convert('var', "\\DDesrosiers\\Controller\\TestController::converter");
```

Controller Providers
====================
If we want to use a ControllerProvider, we can use the annotations service's process() method directly.

```
namespace DDesrosiers\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

class TestProviderController implements ControllerProviderInterface
{

	function connect(Application $app)
	{
		return $app['annot']->process(get_class($this), false, true);
	}

	/**
	 * @SLX\Route(
	 *		@SLX\Request(method="GET", uri="test/{var}"),
	 *		@SLX\Assert(variable="var", regex="\d+"),
	 * )
	 */
	public function testMethod()
	{
		return new Response("test Method");
	}
}
```

The ControllerProviderInterface's connect() requirement was satisfied by calling the annotation service's process() method.  

Service
=======
When registered, an instance of AnnotationService is available via $app['annot'];  The AnnotationService's process() method parses annotations in a class to configure controllers.  It is usually not necessary to use the service directly.
AnnotationService->process() takes 3 arguments:
* **controllerName**: The fully qualified class name of the controller to process.
* **isServiceController**: This matters because Silex expects a different string representation of a controller method for ServiceControllers.  Default: false.
* **newCollection**: If true, all routes found will be put into a new controller collection and that collection will be returned.  Default: false.


Annotations
===========
**@Route**

The @Route annotation groups annotations into an isolated endpoint definition.  This is required if you have multiple aliases for your controller method with different modifiers.  All other annotations can be included as sub-annotations of @Route or stand on their own.

**@Request**

The @Request annotation associates a uri pattern to the controller method.  If multiple @Request annotations are given, all modifiers will be applied to all @Requests unless they wrapped in a @Route annotation.
* method: A valid Silex method (get, post, put, delete, match)
* uri: The uri pattern.

**@Assert**

Silex\Route::assert()
* variable
* regex

**@Convert**

Silex\Route::convert()
* variable
* callback

**@Host**

Silex\Route::host()
* host

**@RequireHttp**

Silex\Route::requireHttp()

**@RequestHttps**

Silex\Route::requireHttps()

**@Value**

Silex\Route::value()
* variable
* default

**@Before**

Silex\Route::before()
* callback

**@After**

Silex\Route::after()
* callback

**@Bind**

Silex\Controller::bind()
* routeName

**@Modifier**

The Modifier annotation is a catch-all to execute any method of the Controller or Route.  All methods should have an annotation, but this annotation is provided as a way to "future-proof" the annotation provider.  In case something is added in the future, users can use it right away instead of waiting for a new annotation to be added.

Silex\Route::{method}()
Silex\Controller::{method}()
* method (name of the method to call on the Route object)
* args (array of arguments to send the the method)
