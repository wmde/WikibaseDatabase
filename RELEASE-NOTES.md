These are the release notes for the [Wikibase Database library](README.md).

## Version 0.3 (dev)

### Compatibility changes

* Removed all Schema code
* Removed all SQL builders
* Removed `PDOQueryInterface` and associated classes
* Removed all escaper interfaces and classes

## Version 0.2 (2014-04-28)

### Compatibility changes

* Removed custom autoloaders is favour of using the Composer autoloader. The client
is thus responsible for autoloading.
* MediaWiki plugin: added compatibility with changes in MediaWiki 1.22.
* Replaced ResultIterator by PHPs native Iterator interface. The interface signature is the same.
* All FieldDefinition::TYPE_ constants are now TypeDefinition::TYPE_ constants
* FieldDefinition::TYPE_BOOL has been changed to TypeDefinition::TYPE_TINYINT
* FieldDefinition::TYPE_TEXT has been changed to TypeDefinition::TYPE_BLOB
* FieldDefinition constructor now takes a TypeDefinition object for param 2 $type.
* FieldDefinition constructor no longer accepts $attributes, this should be passed to a TypeDefinition object
* IndexDefinitions no longer care about index size, $columns in the constructor is now an array of strings

### Additions

* Added PDOQueryInterface, which is an adapter for QueryInterface and a facade delegating
SQL building responsibilities to various SQL builders, which output is then fed to PDO.
* Added PDOTableBuilder
* Added PDOSchemaModifier
* Added PDOFactory for PDO specific service construction
* Added InsertSqlBuilder interface and MySQLInsertSqlBuilder implementation.
* Added UpdateSqlBuilder interface and MySQLUpdateSqlBuilder implementation.
* Added DeleteSqlBuilder interface and MySQLDeleteSqlBuilder implementation.
* Added SelectSqlBuilder interface and MySQLSelectSqlBuilder implementation.
* Added trivial fake escaper and table name formatter in the namespace to improve test
readability and consistency.
* Added support for BIGINTs in both Mysql and Sqlite
* Added support for DECIMAL in both Mysql and Sqlite
* Added support for specifying maz sizes of fields in TypeDefinitions
* Added support for VARCHARs in both Mysql and Sqlite
* Added support for reading back Index size limits in Mysql
* Various select interfaces now accept an array of table names
* Added NullTableNameFormatter and PrefixingTableNameFormatter

### Improvements

* Removed unused UnEscaper interface.
* Split Escaper interface into ValueEscaper and IdentifierEscaper, keeping Escaper itself.
* The PHPUnit bootstrap file now automatically runs composer update.

## Version 0.1 (2013-11-01)

Initial release with the following features:

* QueryInterface interface for select, update, insert and delete queries.
* TableBuilder interface for table creation, deletion and existence checking.
* SchemaModifier interface for adding and removing fields and indexes.
* TableSqlBuilder, FieldSqlBuilder and IndexSqlBuilder interfaces for building SQL.
* TableDefinition, FieldDefinition and IndexDefinition objects.
* MediaWiki plugin implementing the above interfaces.
* MySQL plugin providing implementations for all SQL builders.
* SQLite plugin providing implementations for all SQL builders.
