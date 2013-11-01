These are the release notes for the [Wikibase Database library](README.md).

## Version 0.1 (2013-11-01)

Initial release with the following features:

* QueryInterface interface for select, update, insert and delete queries.
* TableBuilder interface for table creation, deletion and existence checking.
* SchemaModifier interface for adding and removing fields and indexes.
* TableSlbBuilder, FieldSqlBuilder and IndexSqlBuilder interfaces for building SQL.
* MediaWiki plugin implementing the above interfaces.
* MySQL plugin providing implementations for all SQL builders.
* SQLite plugin providing implementations for all SQL builders.
