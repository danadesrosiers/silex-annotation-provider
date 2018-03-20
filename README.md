[![Build Status](https://travis-ci.org/danadesrosiers/silex-annotation-provider.svg?branch=master)](https://travis-ci.org/danadesrosiers/silex-annotation-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/danadesrosiers/silex-annotation-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/danadesrosiers/silex-annotation-provider/?branch=master)

silex-annotation-provider
=========================

A Silex ServiceProvider that defines annotations that can be used in a Silex controller.  Define your controllers in a class and use annotations to setup routes and define modifiers.


Installation
============

Install the silex-annotation-provider using composer.

```json
{
    "require": {
        "ddesrosiers/silex-annotation-provider": "~2.0"
    }
}
```

Registration
============
```php
$app->register(new DDesrosiers\SilexAnnotations\AnnotationServiceProvider(), array(
    "annot.cache" => new ApcCache(),
    "annot.controllerDir" => "$srcDir/Controller",
    "annot.controllerNamespace" => "Company\\Controller\\"
));
```

Parameters
==========
annot.controllerDir
-------------------
Specify the directory in which to search for controllers.  This directory will be searched recursively for classes with the `@Controller` annotation.  Found controller classes will be processed for route annotations.  Either this or annot.controllers is required to locate controllers.  If a cache object is given using the 'annot.cache' option and the 'debug' option is true, the list of controller classes will be cached to improve performance.

annot.controllerNamespace
-------------------------
The base namespace of the controllerDir.  This option works with the annot.controllerDir option.  It is not required, but saves the service from having to do the work of figuring out the namespace of the controller classes.

annot.controllers
-----------------
An array of fully qualified controller names.  If set, the provider will automatically register each controller as a ServiceController and set up routes and modifiers based on annotations found.  Controllers can be grouped into controller collections by grouping them with an associative array using the array key as the mount point.
```php
$app['annot.controllers'] = array(
	"MyControllerNamespace\\Controller1",
	"MyControllerNamespace\\Controller2",
	"MyControllerNamespace\\Controller3",
	"MyControllerNamespace\\Controller4"
);
```
annot.cache
-----------
An instance of a class that implements Doctrine\Common\Cache\Cache.  This is the cache that will be used by the AnnotationReader to cache annotations so they don't have to be parsed every time.  Make sure to include Doctrine Cache as it is not a required dependency of this project.

Annotate Controllers
====================
Create your controller.  The following is an example demonstrating the use of annotations to register an endpoint.
```php
namespace DDesrosiers\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/prefix")
 * @SLX\RequireHttps
 */
class TestController
{
	/**
	 * @SLX\Route(
	 *		@SLX\Request(method="GET", uri="test/{var}"),
	 *		@SLX\Assert(variable="var", regex="\d+"),
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
```php
$controllerCollection = $app['controller_factory'];
$controllerCollection->requireHttps();
$controller->get("test/{var}", "\\DDesrosiers\\Controller\\TestController:testMethod")
	->assert('var', '\d+')
	->convert('var', "\\DDesrosiers\\Controller\\TestController::converter");
$app->mount('/prefix', $controllerCollection);
```

Controller Providers
====================
If we want to use a ControllerProvider, we can use the annotations service's process() method directly.

```php
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

Advanced Options
================
annot.useServiceControllers
---------------------------
Controllers are registered as service controllers by default.  This option can be used to override this default.

annot.controllerFinder
----------------------
Define your own callback to search for controllers.

annot.registerServiceController
-------------------------------
This callback registers the service controller.  Override it if you need to do anything special to register your controllers.

Annotations
===========
**Controller**

The @Controller annotation marks a class as a controller.  The 'prefix' option defines the mount point for the controller collection.

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
