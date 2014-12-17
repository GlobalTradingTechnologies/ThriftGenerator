ThriftGenerator
===============

Generates [apache thrift](http://thrift.apache.org/) definition files based on PHP classes signature.

[![Build Status](https://travis-ci.org/GlobalTradingTechnologies/ThriftGenerator.svg?branch=master)](https://travis-ci.org/GlobalTradingTechnologies/ThriftGenerator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GlobalTradingTechnologies/ThriftGenerator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GlobalTradingTechnologies/ThriftGenerator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GlobalTradingTechnologies/ThriftGenerator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GlobalTradingTechnologies/ThriftGenerator/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gtt/thrift-generator/v/stable.svg)](https://packagist.org/packages/gtt/thrift-generator)
[![Latest Unstable Version](https://poser.pugx.org/gtt/thrift-generator/v/unstable.svg)](https://packagist.org/packages/gtt/thrift-generator)
[![License](https://poser.pugx.org/gtt/thrift-generator/license.svg)](https://packagist.org/packages/gtt/thrift-generator)

What's the purpose?
===================

The main apache thrift's concept is to make possible cross-language services development that can interact with each other.
In order to product code that can be invoked remotely using thrift framework you need firstly provide all target classes
definitions in special [thrift](http://thrift.apache.org/docs/idl) files and then generate service thrift code.
But imagine you already have some server classes and you want to provide some RPC API for them using thrift. In this case manual filling of thrift files for those server classes seems to be annoying.
ThriftGenerator uses class reflection and doc-blocks to introspect class signatures and generate corresponding thrift files automatically for you. This thrift files would be used then to generate thrift service stuff that can be used on server side with the help of original thrift compiler.

Requirements
===================

ThriftGenerator works with PHP classes and requires PHP 5.3 or higher and provides thrift definition files
can be used by thrift compiler 0.9.2 and higher.

Installation
===================

ThriftGenerator can be installed with composer quite easy:
```
composer require gtt/thrift-generator 0.1.0
```
You also can use ThriftGenerator as a standalone library:
```
git clone https://github.com/GlobalTradingTechnologies/ThriftGenerator && cd ThriftGenerator
composer install --prefer-dist
```

Usage
===================

ThriftGenerator has handy OOP interface:

```php
use Gtt\ThriftGenerator\Generator\ThriftGenerator;

$generator = new ThriftGenerator();
$generator
    // set classes that should be introspected in order to generate thrift definition files
    ->setClasses(
        array(
            new ReflectionClass("\Your\Class\Name"),
            new ReflectionClass("\Another\Class\Name")
        )
    )
    // set output dir
    ->setOutputDir("<path to folder that will contain generated thrift definitions>")
    // generate!
    ->generate();
```

See also [demos](demos) and [functional test cases](tests/Fixtures) for more details.

Restrictions
===================

Sinсe ThriftGenerator statically introspects PHP class signature only with use of Reflection functionality so it have no chance to detect and define in thrift IDL any classes that is used 
internally in your classes (in most cases that is not described in class doc-blocks as input params, 
return values or exceptions can be thrown).

Roadmap
===================

It would be nice to have some features that are not implemented yet:

1. Thrift versioning support.
2. PHP classes inheritance introspection.
3. Comments in generated thrift files.
4. Custom doc-block's implementation to support some thrift-related options that can not be reflected from PHP method signatures or doc blocks (container types wide support, one-way calls, enum's and etc).