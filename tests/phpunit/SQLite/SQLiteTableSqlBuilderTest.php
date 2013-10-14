<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\SQLite\SQLiteTableSqlBuilder;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @covers Wikibase\Database\SQLite\SQLiteTableSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseSQLite
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class SQLiteTableSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$this->newInstance();
		$this->assertTrue( true );
	}

	protected function newInstance() {
		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return '-' . $value . '-';
			} ) );

		$mockTableNameFormatter = $this->getMock( 'Wikibase\Database\TableNameFormatter' );
		$mockTableNameFormatter->expects( $this->any() )
			->method( 'formatTableName' )
			->will( $this->returnArgument(0) );

		$mockFieldSqlBuilder = $this->getMockBuilder( 'Wikibase\Database\SQLite\SQLiteFieldSqlBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$mockFieldSqlBuilder->expects( $this->any() )
			->method( 'getFieldSql' )
			->will( $this->returnValue( '<FIELDSQL>' ) );

		$mockIndexSqlBuilder = $this->getMockBuilder( 'Wikibase\Database\SQLite\SQLiteIndexSqlBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$mockIndexSqlBuilder->expects( $this->any() )
			->method( 'getIndexSql' )
			->will( $this->returnValue( '<INDEXSQL>' ) );

		return new SQLiteTableSqlBuilder(
			$mockEscaper,
			$mockTableNameFormatter,
			$mockFieldSqlBuilder,
			$mockIndexSqlBuilder
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

	public function newMockIndex( $name ){
		$mockIndexDefinition = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\IndexDefinition' )
			->disableOriginalConstructor()
			->getMock();
		$mockIndexDefinition->expects( $this->any() )
			->method( 'getName' )
			->will( $this->returnValue( $name ) );
		return $mockIndexDefinition;
	}

	public function tableAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array( $this->newMockField( 'foo' ) )
			),
			'CREATE TABLE tableName (<FIELDSQL>);'
		);

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array( $this->newMockField( 'foo' ), $this->newMockField( 'bar' ), $this->newMockField( 'baz' ) ),
				array( $this->newMockIndex( 'ham' ) )
			),
			'CREATE TABLE tableName (<FIELDSQL>, <FIELDSQL>, <FIELDSQL>);' . PHP_EOL
			. '<INDEXSQL>'
		);

		$argLists[] = array(
			new TableDefinition(
				'tableName',
				array( $this->newMockField( 'foo' ), $this->newMockField( 'bar' ), $this->newMockField( 'baz' ) ),
				array( $this->newMockIndex( 'ham' ), $this->newMockIndex( 'egg' ) )
			),
			'CREATE TABLE tableName (<FIELDSQL>, <FIELDSQL>, <FIELDSQL>);' . PHP_EOL
			. '<INDEXSQL>' . PHP_EOL
			. '<INDEXSQL>'
		);

		return $argLists;
	}

}
