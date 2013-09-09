<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\FieldDefinition;
use Wikibase\Database\IndexDefinition;
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
 * @author Adam Shorland
 */
class SQLiteTableSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	const DB_NAME = 'dbName';


	public function testCanConstruct() {
		$this->newInstance();
		$this->assertTrue( true );
	}

	protected function newInstance() {

		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedValue' )
			->will( $this->returnArgument(0) );

		$mockTableNameFormatter = $this->getMock( 'Wikibase\Database\TableNameFormatter' );
		$mockTableNameFormatter->expects( $this->any() )
			->method( 'formatTableName' )
			->will( $this->returnArgument(0) );

		return new SQLiteTableSqlBuilder(
			self::DB_NAME,
			$mockEscaper,
			$mockTableNameFormatter
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
						FieldDefinition::NO_ATTRIB
					),
					new FieldDefinition(
						'textField',
						FieldDefinition::TYPE_TEXT
					),
					new FieldDefinition(
						'intField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL, 42
					),
				)
			),
			'CREATE TABLE dbNametableName (primaryField INT NOT NULL, textField BLOB NULL, intField INT DEFAULT 42 NOT NULL);'
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
						FieldDefinition::NO_ATTRIB
					),
					new FieldDefinition(
						'textField',
						FieldDefinition::TYPE_TEXT
					),
					new FieldDefinition(
						'intField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL, 42
					),
				),
				array(
					new IndexDefinition(
						'indexName',
						array( 'intField' => 0, 'textField' => 0 ),
						IndexDefinition::TYPE_INDEX
					),
				)
			),
			'CREATE TABLE dbNametableName (primaryField INT NOT NULL, textField BLOB NULL, intField INT DEFAULT 42 NOT NULL);CREATE INDEX indexName ON dbNametableName (intField,textField);'
		);

		return $argLists;
	}

}
