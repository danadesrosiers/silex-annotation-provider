[![Latest Stable Version](https://poser.pugx.org/ddesrosiers/silex-annotation-provider/v/stable)](https://packagist.org/packages/ddesrosiers/silex-annotation-provider)
[![Build Status](https://travis-ci.org/danadesrosiers/silex-annotation-provider.svg?branch=master)](https://travis-ci.org/danadesrosiers/silex-annotation-provider)
[![Total Downloads](https://poser.pugx.org/ddesrosiers/silex-annotation-provider/downloads)](https://packagist.org/packages/ddesrosiers/silex-annotation-provider)
[![License](https://poser.pugx.org/ddesrosiers/silex-annotation-provider/license)](https://packagist.org/packages/ddesrosiers/silex-annotation-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/danadesrosiers/silex-annotation-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/danadesrosiers/silex-annotation-provider/?branch=master)

silex-annotation-provider
=========================

A Silex ServiceProvider that defines annotations that can be used in a Silex controller.  Define your controllers in a class and use annotations to setup routes and define modifiers.

Changes in v3
-------------
* Redesigned annotation format.  No more Doctrine Annotations.
* Uses PSR-16 cache instead of Doctrine Cache.
* Simplified feature set.  Removes support for ServiceProviders, custom Service Controller registration, and other obscure customization options in favor of simplicity.
* Minimum PHP version is now 7.1.  No official support for HHVM.

Installation
============

Install the silex-annotation-provider using composer.

```json
{
    "require": {
        "ddesrosiers/silex-annotation-provider": "~3.0"
    }
}
```

Registration
============
```php
$app->register(new DDesrosiers\SilexAnnotations\AnnotationServiceProvider(), array(
    "annot.cache" => new MyPsr16Cache(),
    "annot.controllerDir" => "$srcDir/Controller",
    "annot.controllers" => [
        MyClass1::class,
        MyClass2::class
    ]
));
```

Parameters
==========
annot.controllerDir
-------------------
Specify the directory in which to search for controllers.  This directory will be searched recursively for classes with the `@Controller` annotation.  Found controller classes will be processed for route annotations.  Either this or annot.controllers is required to locate controllers.

annot.controllers
-----------------
An array of fully qualified controller names.  If set, the provider will automatically register each controller as a ServiceController and set up routes and modifiers based on annotations found.

annot.cache
-----------
An instance of a class that implements Psr\SimpleCache\CacheInterface.  This cache is used to cache annotation and the controller list to improve performance.

Faster Controller Registration
==============================
Silex has to register every endpoint in your app on every request.  If you have a lot of endpoints, that could be a significant overhead on each and every request.  Silex Annotations can improve this by filtering the controllers that need to be registered using the `prefix` on the `Controller` annotation.  We only need to register the endpoints in the Controller if the prefix matches the URI.  In this way, Silex Annotations allows FASTER routing than pure Silex.

Annotate Controllers
====================
Create your controller.  The following is an example demonstrating the use of annotations to register an endpoint.
```php
namespace DDesrosiers\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller(
 *     prefix => test
 *     after => \DDesrosiers\Controller\TestController::converter
 *     host => www.test.com
 *     requireHttp
 *     secure => ADMIN
 * )
 */
class TestController
{
    /**
     * @Route(
     *     uri => GET test/{var}
     *     assert => var, \d+
     *     convert => var, \DDesrosiers\Controller\TestController::converter
     *     after => \DDesrosiers\Controller\TestController::converter
     *     host => www.test.com
     *     requireHttps
     *     secure => DEV
     *     value => var, default
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
$controllerCollection = $app['controller_factory']
    ->after('\DDesrosiers\Controller\TestController::converter');
    ->host('www.test.com');
    ->requireHttp();
    ->secure('ADMIN');
$controllerCollection->get("test/{var}", "\\DDesrosiers\\Controller\\TestController:testMethod")
    ->assert('var', '\d+')
    ->convert('var', "\\DDesrosiers\\Controller\\TestController::converter");
    ->host('www.test.com')
    ->requireHttps()
    ->secure('DEV')
    ->value('var', 'default');
$app->mount('/prefix', $controllerCollection);
```

Annotations
===========
**Controller**

The @Controller annotation marks a class as a controller.  The 'prefix' option defines the mount point for the controller collection.  The prefix must be the first option.

**@Route**

The @Route annotation defines an endpoint.  'uri' is required and must be the first option defined.

Short Annotation Notation
=========================
In the Controller annotation, if prefix is the only option needed, the 'prefix' key can be omitted.

In the Route annotation, if uri is the only option needed, the 'uri' key can be omitted.
```php
   namespace DDesrosiers\Controller;
   
   use Symfony\Component\HttpFoundation\Response;
   
   /**
    * @Controller(test)
    */
   class TestController
   {
       /**
        * @Route(GET test/{var})
        */
       public function testMethod($var)
       {
           return new Response("test Method: $var");
       }
   }
```
