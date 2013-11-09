These are the release notes for the [Wikibase Database library](README.md).

## Version 0.2 (under development)

* Removed custom autoloaders is favour of using the Composer autoloader.
* MediaWiki plugin: added compatibility with changes in MediaWiki 1.22.

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
