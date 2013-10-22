<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\SQLite\SQLiteIndexSqlBuilder;

/**
 * @covers Wikibase\Database\SQLite\SQLiteIndexSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseSQLite
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteIndexSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider fieldAndSqlProvider
	 */
	public function testGetCreateTableSql( IndexDefinition $index, $expectedSQL ) {
		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return '-' . $value . '-';
			} ) );

		$mockTableNameFormatter = $this->getMock( 'Wikibase\Database\TableNameFormatter' );
		$mockTableNameFormatter->expects( $this->any() )
			->method( 'formatTableName' )
			->will( $this->returnCallback( function( $tableName ) {
				return 'prefix_' . $tableName;
			} ) );

		$sqlBuilder = new SQLiteIndexSqlBuilder( $mockEscaper, $mockTableNameFormatter );
		$sql = $sqlBuilder->getIndexSQL( $index, 'tableName' );
		$this->assertEquals( $expectedSQL, $sql );
	}

	public function fieldAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_INDEX
			),
			'CREATE INDEX -indexName- ON -prefix_tableName- (-intField-,-textField-);'
		);


		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_UNIQUE
			),
			'CREATE UNIQUE INDEX -indexName- ON -prefix_tableName- (-intField-,-textField-);'
		);

		return $argLists;
	}

	public function testUnsupportedType() {
		$this->setExpectedException( 'RuntimeException', 'does not support db indexes of type' );

		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return '-' . $value . '-';
			} ) );

		$tableNameFormatter = $this->getMockBuilder( 'Wikibase\Database\MediaWiki\MediaWikiTableNameFormatter' )
			->disableOriginalConstructor()
			->getMock();

		$indexDefinition = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\IndexDefinition' )
			->disableOriginalConstructor()
			->getMock();
		$indexDefinition->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( 'foobar' ) );

		$sqlBuilder = new SQLiteIndexSqlBuilder( $mockEscaper, $tableNameFormatter );
		$sqlBuilder->getIndexSQL( $indexDefinition, 'tableName' );
	}

}