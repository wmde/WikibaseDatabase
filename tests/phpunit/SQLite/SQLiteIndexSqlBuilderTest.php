<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\SQLite\SQLiteIndexSqlBuilder;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter;

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

		$tableNameFormatter = new FakeTableNameFormatter();

		$sqlBuilder = new SQLiteIndexSqlBuilder( $mockEscaper, $tableNameFormatter );
		$sql = $sqlBuilder->getIndexSQL( $index, 'tableName' );
		$this->assertEquals( $expectedSQL, $sql );
	}

	public function fieldAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField', 'textField' ),
				IndexDefinition::TYPE_INDEX
			),
			'CREATE INDEX -prefix_tableName-indexName- ON -prefix_tableName- (-intField-,-textField-);'
		);


		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField', 'textField' ),
				IndexDefinition::TYPE_UNIQUE
			),
			'CREATE UNIQUE INDEX -prefix_tableName-indexName- ON -prefix_tableName- (-intField-,-textField-);'
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