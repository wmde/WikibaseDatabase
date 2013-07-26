<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\FieldDefinition;
use Wikibase\Database\SQLite\SQLiteTableSqlBuilder;
use Wikibase\Database\TableDefinition;

/**
 * @covers Wikibase\Database\SQLite\SQLiteTableSqlBuilder
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseDatabaseTest
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SQLiteTableSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	const DB_NAME = 'dbName';


	public function testCanConstruct() {
		$this->newInstance();
		$this->assertTrue( true );
	}

	protected function newInstance() {
		return new SQLiteTableSqlBuilder(
			self::DB_NAME,
			$this->getMock( 'Wikibase\Database\Escaper' )
		);
	}

	/**
	 * @dataProvider tableAndSqlProvider
	 */
	public function testGetCreateTableSql( TableDefinition $table, $expectedSQL ) {
		$sqlBuilder = $this->newInstance();

		$actualSQL = $sqlBuilder->getCreateTableSql( $table );

		$this->assertEquals( $expectedSQL, $actualSQL );
	}

	public function tableAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array(
					new FieldDefinition( 'fieldName', FieldDefinition::TYPE_INTEGER )
				)
			),
			'CREATE TABLE dbNametableName (fieldName INT NULL);'
		);

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array(
					new FieldDefinition(
						'primaryField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL,
						FieldDefinition::NO_DEFAULT,
						FieldDefinition::NO_ATTRIB,
						FieldDefinition::INDEX_PRIMARY
					),
					new FieldDefinition(
						'textField',
						FieldDefinition::TYPE_TEXT
					),
					new FieldDefinition(
						'intField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL,
						42
					),
				)
			),
			'CREATE TABLE dbNametableName (primaryField INT NOT NULL INTEGER PRIMARY KEY, textField BLOB NULL, intField INT DEFAULT  NOT NULL);'
		);

		return $argLists;
	}

}
