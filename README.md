# Wikibase Database

[![Build Status](https://secure.travis-ci.org/wmde/WikibaseDatabase.png?branch=master)](http://travis-ci.org/wmde/WikibaseDatabase)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/wmde/WikibaseDatabase/badges/quality-score.png?s=9199e94150e3441712ce0c311941e4e1ea0b730a)](https://scrutinizer-ci.com/g/wmde/WikibaseDatabase/)

On Packagist:
[![Latest Stable Version](https://poser.pugx.org/wikibase/database/version.png)](https://packagist.org/packages/wikibase/database)
[![Download count](https://poser.pugx.org/wikibase/database/d/total.png)](https://packagist.org/packages/wikibase/database)

Wikibase Database provides a simple interface for database interaction. This interface is inspired
by the MediaWiki database abstraction layer.

Wikibase Database does not do any of the hard work itself, but rather contains adapters to existing
database abstraction layers, such as [Doctrine DBAL](http://www.doctrine-project.org/projects/dbal.html)
and the MediaWiki database abstraction layer.

View the [release notes](RELEASE-NOTES.md) for recent changes to Wikibase Database.

## Requirements

* PHP 5.3 or later
* When using the MediaWiki plugin: MediaWiki 1.21 or later
* When using the Doctrine DBAL plugin: DBAL 2.4 or later

## Installation

You can use [Composer](http://getcomposer.org/) to download and install
this package as well as its dependencies. Alternatively you can simply clone
the git repository and take care of loading yourself.

### Composer

To add this package as a local, per-project dependency to your project, simply add a
dependency on `wikibase/database` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Wikibase Database 0.1:

```json
{
	"require": {
		"wikibase/database": "0.1.*"
	}
}
```

### Manual

Get the Wikibase Database code, either via git, or some other means. Also get all dependencies.
You can find a list of the dependencies in the "require" section of the composer.json file.
Load all dependencies and the load the Wikibase Database library by including its entry point:
WikibaseDatabase.php.

## Tests

This library comes with a set up PHPUnit tests that cover all non-trivial code. You can run these
tests using the PHPUnit configuration file found in the root directory. The tests can also be run
via TravisCI, as a TravisCI configuration file is also provided in the root directory.

### Running the tests

The tests in `tests/phpunit` can be run without any setup
(with the exception of `tests/phpunit/MediaWiki`). You can run these with:

```sh
phpunit --testsuite=WikibaseDatabaseUnit
```

To include the PDO based integration tests, first setup the test database (if not already done so):

```bash
mysql --user root -p < tests/createTestDB.sql
```

Then run:

```bash
phpunit --testsuite=WikibaseDatabaseStandalone
```

The test database can be removed with:

```bash
mysql --user root -p < tests/dropTestDB.sql
```

To also run the MediaWiki based tests, create a MediaWiki install, and include Wikibase Database
as an extension to it. Then run

```bash
phpunit
```

## Using the abstraction layer

This section serves to give you a quick idea. For more detailed documentation,
see the definitions of the individual interfaces.

QueryInterface:

```php
$db->select(
    'tableName'
    array( 'field_one', 'field_two' ),
    array(
        'condition' => 'value',
        'awesome > 9000'
    )
);
```

```php
$db->delete(
    'tableName',
    array(
        'condition' => 'value',
        'awesome > 9000'
    )
);
```

```php
$db->update(
    'tableName',
    array(
        'field_one' => 'new value',
        'field_two' => '~=[,,_,,]:3',
    ).
    array(
        'condition' => 'value',
        'awesome > 9000'
    )
);
```

```php
$db->insert(
    'tableName',
    array(
        'field_one' => 'value',
        'field_two' => '~=[,,_,,]:3',
    )
);
```

```php
$db->getInsertId();
```

## Abstraction layer structure

All classes of this component reside in the `Wikibase\Database namespace`, which is PSR-0 mapped
onto the `src/` directory. The component has several sub packages:

### QueryInterface

The main interface of this component is QueryInterface. It defines methods for interacting with
a database. These methods include insert, update, delete and select. When using this component,
you will likely be passing around an instance of an implementation of this interface.

This package is mostly abstract, fully public and has no dependencies outside of its own namespace.

### Plugins

Plugins depend on Wikibase Database. NOTHING in Wikibase Database depends on them.

Plugins are typically both concrete and public.

#### Plugin: MediaWiki

MediaWiki implementations of various interfaces. Most of these are adapters for
MediaWikis DatabseBase interface that abstract away the bad design this one contains.

### Top level namespace

The top level namespace, Wikibase\Database, contains some interfaces that do not
fit in any of the more specific packages.

Currently it contains DBConnectionProvider and LazyDBConnectionProvider which both depend
on MediaWiki. This is due to legacy reasons, and should not be relied upon, as these
will be moved.

## Authors

Wikibase Database has been written by [Jeroen De Dauw](https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw)
and Adam Shorland as [Wikimedia Germany](https://wikimedia.de) employees for the
[Wikidata project](https://wikidata.org/).

Contributions where also made by [several others](https://www.ohloh.net/p/wikibasedatabase/contributors).

## Links

* [Wikibase Database on Packagist](https://packagist.org/packages/wikibase/database)
* [Wikibase Database on Ohloh](https://www.ohloh.net/p/wikibasedatabase)
* [Wikibase Database on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Wikibase_Database)
* [Wikibase Database on TravisCI](https://travis-ci.org/wmde/WikibaseDatabase)
* [Wikibase Database on ScrutinizerCI](https://scrutinizer-ci.com/g/wmde/WikibaseDatabase/)
