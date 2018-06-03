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
    "annot.cache" => new MyPsr16Cache(),
    "annot.controllerDir" => "$srcDir/Controller"
));
```

Parameters
==========
annot.controllerDir
-------------------
Specify the directory in which to search for controllers.  This directory will be searched recursively for classes with the `@Controller` annotation.  Found controller classes will be processed for route annotations.  Either this or annot.controllers is required to locate controllers.  If a cache object is given using the 'annot.cache' option and the 'debug' option is true, the list of controller classes will be cached to improve performance.

annot.controllers
-----------------
An array of fully qualified controller names.  If set, the provider will automatically register each controller as a ServiceController and set up routes and modifiers based on annotations found.  Controllers can be grouped into controller collections by grouping them with an associative array using the array key as the mount point.
```php
$app['annot.controllers'] = array(
	Controller1::class,
	Controller2::class,
	Controller3::class,
	Controller4::class
);
```
annot.cache
-----------
An instance of a class that implements Doctrine\Common\Cache\Cache.  This is the cache that will be used by the AnnotationReader to cache annotations so they don't have to be parsed every time.  Make sure to include Doctrine Cache as it is not a required dependency of this project.

annot.base_uri (Enables Faster Controller Registration)
--------------
This is the base uri of all requests.  Basically, it's the part of the URI that isn't included in `$_SERVER['REQUEST_URI']`.  If your bootstrap file is at the root of htdocs, the value is "/".  If your bootstrap file lives in a directory called "api", all your API's URIs are prefixed with "/api" and that is value you must specify for annot.uri.

annot.base_uri enables faster registration of controllers.  Silex has to register every endpoint in your app on every request.  If you have a lot of endpoints, that could be a significant overhead on each and every request.  Silex Annotations can improve this by filtering the controllers that need to be registered using the `prefix` on the `Controller` annotation.  We only need to register the endpoints in the Controller if the prefix matches the URI.  In this way, Silex Annotations allows FASTER routing than pure Silex. 

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

Annotations
===========
**Controller**

The @Controller annotation marks a class as a controller.  The 'prefix' option defines the mount point for the controller collection.

**@Route**

The @Route annotation groups annotations into an isolated endpoint definition.  This is required if you have multiple aliases for your controller method with different modifiers.  All other annotations can be included as sub-annotations of @Route or stand on their own.

**@Request**

The @Request annotation associates a uri pattern to the controller method.
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
