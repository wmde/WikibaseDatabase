<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\FieldDefinition;
use Wikibase\Database\MySQL\MySqlTableSqlBuilder;
use Wikibase\Database\TableDefinition;

/**
 * @covers Wikibase\Database\MySQL\MySQLTableSqlBuilder
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
class MySQLTableSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	const DB_NAME = 'dbName';
	const TABLE_PREFIX = 'prefix_';

	public function testCanConstruct() {
		$this->newInstance();
		$this->assertTrue( true );
	}

	protected function newInstance() {
		return new MySqlTableSqlBuilder(
			self::DB_NAME,
			self::TABLE_PREFIX,
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
			'CREATE TABLE `dbName`.prefix_tableName (fieldName INT NULL) ENGINE=InnoDB, DEFAULT CHARSET=binary'
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
			'CREATE TABLE `dbName`.prefix_tableName (primaryField INT NOT NULL PRIMARY KEY AUTO_INCREMENT, textField BLOB NULL, intField INT DEFAULT  NOT NULL) ENGINE=InnoDB, DEFAULT CHARSET=binary'
		);

		return $argLists;
	}

}
