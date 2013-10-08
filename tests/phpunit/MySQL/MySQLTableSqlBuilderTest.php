<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\MySQL\MySQLTableSqlBuilder;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @covers Wikibase\Database\MySQL\MySQLTableSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MySQLTableSqlBuilderTest extends \PHPUnit_Framework_TestCase {

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

		return new MySQLTableSqlBuilder(
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
			'CREATE TABLE `dbName`.tableName (fieldName INT NULL) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array(
					new FieldDefinition(
						'primaryField', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, FieldDefinition::NO_DEFAULT, FieldDefinition::NO_ATTRIB
					),
					new FieldDefinition(
						'textField', FieldDefinition::TYPE_TEXT
					),
					new FieldDefinition(
						'intField', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42
					),
				)
			),
			'CREATE TABLE `dbName`.tableName (primaryField INT NOT NULL, textField BLOB NULL, intField INT DEFAULT 42 NOT NULL) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);


		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array(
					new FieldDefinition(
						'textField', FieldDefinition::TYPE_TEXT
					),
					new FieldDefinition(
						'intField', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42
					),
				),
				array(
					new IndexDefinition(
						'indexName', array( 'textField' => 0, 'intField' => 0 ), IndexDefinition::TYPE_INDEX
					),
				)
			),
			'CREATE TABLE `dbName`.tableName (textField BLOB NULL, intField INT DEFAULT 42 NOT NULL, INDEX `indexName` (`textField`,`intField`)) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array(
					new FieldDefinition(
						'textField', FieldDefinition::TYPE_TEXT
					),
					new FieldDefinition(
						'intField', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42
					),
					new FieldDefinition(
						'textField2', FieldDefinition::TYPE_TEXT
					),
				),
				array(
					new IndexDefinition(
						'indexName', array( 'intField' => 0 ), IndexDefinition::TYPE_INDEX
					),
					new IndexDefinition(
						'uniqueIndexName', array( 'textField2' => 0 ), IndexDefinition::TYPE_UNIQUE
					),
				)
			),
			'CREATE TABLE `dbName`.tableName (textField BLOB NULL, intField INT DEFAULT 42 NOT NULL, textField2 BLOB NULL, INDEX `indexName` (`intField`), UNIQUE INDEX `uniqueIndexName` (`textField2`)) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);

		return $argLists;
	}

}
