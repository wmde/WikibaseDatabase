<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLTableSqlBuilder;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter;

/**
 * @covers Wikibase\Database\MySQL\MySQLTableSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
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
			->will( $this->returnCallback( function( $input ) {
				return "|$input|";
			} ) );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return '-' . $value . '-';
			} ) );

		$tableNameFormatter = new FakeTableNameFormatter();

		$mockFieldSqlBuilder = $this->getMockBuilder( 'Wikibase\Database\MySQL\MySQLFieldSqlBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$mockFieldSqlBuilder->expects( $this->any() )
			->method( 'getFieldSql' )
			->will( $this->returnValue( '<FIELDSQL>' ) );

		return new MySQLTableSqlBuilder(
			self::DB_NAME,
			$mockEscaper,
			$tableNameFormatter,
			$mockFieldSqlBuilder
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

	public function newMockField( $name ){
		$mockFieldDefinition = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\FieldDefinition' )
			->disableOriginalConstructor()
			->getMock();
		$mockFieldDefinition->expects( $this->any() )
			->method( 'getName' )
			->will( $this->returnValue( $name ) );
		return $mockFieldDefinition;
	}

	public function tableAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array( $this->newMockField( 'foo' ) )
			),
			'CREATE TABLE `dbName`.-prefix_tableName- (<FIELDSQL>) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array( $this->newMockField( 'foo' ), $this->newMockField( 'bar' ), $this->newMockField( 'baz' ) )
			),
			'CREATE TABLE `dbName`.-prefix_tableName- (<FIELDSQL>, <FIELDSQL>, <FIELDSQL>) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);


		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array( $this->newMockField( 'foo' ), $this->newMockField( 'bar' ) ),
				array(
					new IndexDefinition(
						'indexName', array( 'textField' => 0, 'intField' => 0 ), IndexDefinition::TYPE_INDEX
					),
				)
			),
			'CREATE TABLE `dbName`.-prefix_tableName- (<FIELDSQL>, <FIELDSQL>, INDEX -indexName- (-textField-,-intField-)) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array( $this->newMockField( 'foo' ), $this->newMockField( 'bar' ), $this->newMockField( 'baz' ) ),
				array(
					new IndexDefinition(
						'indexName', array( 'intField' => 0 ), IndexDefinition::TYPE_INDEX
					),
					new IndexDefinition(
						'uniqueIndexName', array( 'textField2' => 0 ), IndexDefinition::TYPE_UNIQUE
					),
				)
			),
			'CREATE TABLE `dbName`.-prefix_tableName- (<FIELDSQL>, <FIELDSQL>, <FIELDSQL>, INDEX -indexName- (-intField-), UNIQUE INDEX -uniqueIndexName- (-textField2-)) ENGINE=InnoDB, DEFAULT CHARSET=binary;'
		);

		return $argLists;
	}

}
