<?php
namespace DJDesrosiers\Silex\Annotations;

/**
 * @Annotation
 * @Target("METHOD")
 */
class GET
{
	/** @var string */
	public $uri;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class POST
{
	/** @var string */
	public $uri;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class PUT
{
	/** @var string */
	public $uri;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class DELETE
{
	/** @var string */
	public $uri;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class MATCH
{
	/** @var string */
	public $uri;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Assert
{
	/** @var string */
	public $variable;

	/** @var string */
	public $regex;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Value
{
	/** @var string */
	public $variable;

	/** @var mixed */
	public $default;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Convert
{
	/** @var string */
	public $variable;

	/** @var string */
	public $callback;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Host
{
	/** @var string */
	public $host;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class RequireHttp
{

}

/**
 * @Annotation
 * @Target("METHOD")
 */
class RequireHttps
{

}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Before
{
	/** @var string */
	public $callback;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class After
{
	/** @var string */
	public $callback;
}

