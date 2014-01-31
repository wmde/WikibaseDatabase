<?php

namespace Wikibase\Database\Tests;

use PDO;
use Wikibase\Database\MySQL\MySQLDeleteSqlBuilder;
use Wikibase\Database\MySQL\MySQLInsertSqlBuilder;
use Wikibase\Database\MySQL\MySQLSelectSqlBuilder;
use Wikibase\Database\MySQL\MySQLUpdateSqlBuilder;
use Wikibase\Database\PDO\PDOQueryInterface;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Integration
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLTest extends \PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
		self::markTestSkipped();

		exec( "mysql -e 'create database test_wikibase_database;'" );

		$table = new TableDefinition(
			'test_table',
			array(
				new FieldDefinition(
					'row_id',
					FieldDefinition::TYPE_INTEGER
				),
				new FieldDefinition(
					'some_text',
					FieldDefinition::TYPE_TEXT
				)
			)
		);


	}

	public static function tearDownAfterClass() {
		exec( "mysql -e 'drop database test_wikibase_database;'" );
	}

	public function testInsert() {
		$pdo = new PDO( 'mysql:host=localhost;dbname=test_wikibase_database;charset=utf8', 'root', '' );

		$queryInterface = new PDOQueryInterface(
			$pdo,
			new MySQLInsertSqlBuilder(),
			new MySQLUpdateSqlBuilder(),
			new MySQLDeleteSqlBuilder(),
			new MySQLSelectSqlBuilder()
		);

//		$queryInterface->insert(  );
	}

}
