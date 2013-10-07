<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\SQLite\SQLiteIndexSqlBuilder;
use Wikibase\Database\Schema\Definitions\IndexDefinition;

/**
 * @covers Wikibase\Database\SQLite\SQLiteIndexSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
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
		$mockTableNameFormatter = $this->getMock( 'Wikibase\Database\TableNameFormatter' );
		$mockTableNameFormatter->expects( $this->any() )
			->method( 'formatTableName' )
			->will( $this->returnArgument(0) );

		$sqlBuilder = new SQLiteIndexSqlBuilder( $mockTableNameFormatter );
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
			'CREATE INDEX indexName ON tableName (intField,textField);'
		);


		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_UNIQUE
			),
			'CREATE UNIQUE INDEX indexName ON tableName (intField,textField);'
		);

		return $argLists;
	}

}