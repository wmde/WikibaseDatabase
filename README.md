[![Build Status](https://secure.travis-ci.org/wikimedia/mediawiki-extensions-WikibaseDatabase.png?branch=master)](http://travis-ci.org/wikimedia/mediawiki-extensions-WikibaseDatabase)

These is the readme file for the Wikibase Database component.

[Extension page on mediawiki.org](https://www.mediawiki.org/wiki/Extension:Wikibase_Database)

[Latest version of the readme file](https://gerrit.wikimedia.org/r/gitweb?p=mediawiki/extensions/WikibaseDatabase.git;a=blob;f=README.md)

About
=====

Wikibase Database is a simple database abstraction layer. It is inspired by the MediaWiki database
abstraction layer and both improves and extends on it.

`$db->select(
    array( 'field_one', 'field_two' ),
    array(
        'foo' => 'bar',
        'awesome > 9000'
    )
);`

`$db->createTable( new TableDefinition(
    'table_name',
    array(
        new FieldDefinition( ... ),
        ...
    )
) );`