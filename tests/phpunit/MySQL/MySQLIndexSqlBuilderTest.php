<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLIndexSqlBuilder;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter;

/**
 * @covers Wikibase\Database\MySQL\MySQLIndexSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MySQLIndexSqlBuilderTest extends \PHPUnit_Framework_TestCase {

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

		$mockTableNameFormatter = new FakeTableNameFormatter();

		$sqlBuilder = new MySQLIndexSqlBuilder( $mockEscaper, $mockTableNameFormatter );
		$sql = $sqlBuilder->getIndexSQL( $index, 'tableName' );
		$this->assertEquals( $expectedSQL, $sql );
	}

	public function fieldAndSqlProvider() {
		$argLists = array();

		//TODO test with type TYPE_SPATIAL
		//TODO test with type TYPE_FULLTEXT

		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_INDEX
			),
			'CREATE INDEX -indexName- ON -prefix_tableName- (-intField-,-textField-)'
		);

		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_UNIQUE
			),
			'CREATE UNIQUE INDEX -indexName- ON -prefix_tableName- (-intField-,-textField-)'
		);

		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_PRIMARY
			),
			'CREATE PRIMARY KEY ON -prefix_tableName- (-intField-,-textField-)'
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

		$indexDefinition->expects( $this->atLeastOnce() )
			->method( 'getType' )
			->will( $this->returnValue( 'foobar' ) );

		$sqlBuilder = new MySQLIndexSqlBuilder( $mockEscaper, $tableNameFormatter );
		$sqlBuilder->getIndexSQL( $indexDefinition, 'tableName' );
	}

}