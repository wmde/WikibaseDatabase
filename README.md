# Wikibase Database

[![Latest Stable Version](https://poser.pugx.org/wikibase/database/version.png)](https://packagist.org/packages/wikibase/database)
[![Latest Stable Version](https://poser.pugx.org/wikibase/database/d/total.png)](https://packagist.org/packages/wikibase/database)
[![Build Status](https://secure.travis-ci.org/wikimedia/mediawiki-extensions-WikibaseDatabase.png?branch=master)](http://travis-ci.org/wikimedia/mediawiki-extensions-WikibaseDatabase)

Wikibase Database is a simple database abstraction layer. It is inspired by the MediaWiki database
abstraction layer and both improves and extends on it.

## Requirements

* PHP 5.3 or later
* MediaWiki 1.21 or later, only when using MediaWikiQueryInterface

## Installation

You can use [Composer](http://getcomposer.org/) to download and install
this package as well as its dependencies. Alternatively you can simply clone
the git repository and take care of loading yourself.

### Composer

To add this package as a local, per-project dependency to your project, simply add a
dependency on `wikibase/database` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Wikibase Database 1.0:

    {
        "require": {
            "wikibase/database": "1.0.*"
        }
    }

### Manual

Get the Wikibase Database code, either via git, or some other means. Also get all dependencies.
You can find a list of the dependencies in the "require" section of the composer.json file.
Load all dependencies and the load the Wikibase Database library by including its entry point:
WikibaseDatabase.php.

## Usage

```php
$db->select(
    array( 'field_one', 'field_two' ),
    array(
        'foo' => 'bar',
        'awesome > 9000'
    )
);
```

```php
$db->createTable( new TableDefinition(
    'table_name',
    array(
        new FieldDefinition( ... ),
        ...
    )
) );
```

## Tests

This library comes with a set up PHPUnit tests that cover all non-trivial code. You can run these
tests using the PHPUnit configuration file found in the root directory. The tests can also be run
via TravisCI, as a TravisCI configuration file is also provided in the root directory.

## Authors

Wikibase Database has been written by [Jeroen De Dauw](https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw)
as [Wikimedia Germany](https://wikimedia.de) employee for the [Wikidata project](https://wikidata.org/).

## Links

* [Wikibase Database on Packagist](https://packagist.org/packages/wikibase/database)
* [Wikibase Database on Ohloh](https://www.ohloh.net/p/wikibasedatabase)
* [Wikibase Database on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Wikibase_Database)
* [TravisCI build status](https://travis-ci.org/wikimedia/mediawiki-extensions-WikibaseDatabase)
* [Latest version of the readme file](https://github.com/wikimedia/mediawiki-extensions-WikibaseDatabase/blob/master/README.md)
