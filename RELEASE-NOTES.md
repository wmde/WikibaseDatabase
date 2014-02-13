These are the release notes for the [Wikibase Database library](README.md).

## Version 0.2 (under development)

### Compatibility changes

* Removed custom autoloaders is favour of using the Composer autoloader. The client
is thus responsible for autoloading.
* MediaWiki plugin: added compatibility with changes in MediaWiki 1.22.
* Replaced ResultIterator by PHPs native Iterator interface. The interface signature is the same.
* FieldDefinition::TYPE_BOOL has been changed to FieldDefinition::TYPE_TINYINT

### Additions

* Added PDOQueryInterface, which is an adapter for QueryInterface and a facade delegating
SQL building responsibilities to various SQL builders, which output is then fed to PDO.
* Added InsertSqlBuilder interface and MySQLInsertSqlBuilder implementation.
* Added UpdateSqlBuilder interface and MySQLUpdateSqlBuilder implementation.
* Added DeleteSqlBuilder interface and MySQLDeleteSqlBuilder implementation.
* Added SelectSqlBuilder interface and MySQLSelectSqlBuilder implementation.
* Added trivial fake escapes and table name formatter in the namespace to improve test
readability and consistency.
* Added support for BIGINTs in both Mysql and Sqlite
* Added support for DECIMAL in both Mysql and Sqlite

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
