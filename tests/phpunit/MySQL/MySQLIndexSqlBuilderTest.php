<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLIndexSqlBuilder;
use Wikibase\Database\Schema\Definitions\IndexDefinition;

/**
 * @covers Wikibase\Database\MySQL\MySQLIndexSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
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
		$mockTableNameFormatter = $this->getMock( 'Wikibase\Database\TableNameFormatter' );
		$mockTableNameFormatter->expects( $this->any() )
			->method( 'formatTableName' )
			->will( $this->returnArgument(0) );

		$sqlBuilder = new MySQLIndexSqlBuilder( $mockTableNameFormatter );
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
			'CREATE INDEX `indexName` ON tableName (`intField`,`textField`)'
		);

		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_UNIQUE
			),
			'CREATE UNIQUE INDEX `indexName` ON tableName (`intField`,`textField`)'
		);

		$argLists[] = array(
			new IndexDefinition(
				'indexName',
				array( 'intField' => 0, 'textField' => 0 ),
				IndexDefinition::TYPE_PRIMARY
			),
			'CREATE PRIMARY KEY ON tableName (`intField`,`textField`)'
		);

		return $argLists;
	}

	public function testUnsupportedType(){
		$this->markTestIncomplete( 'Test RuntimeException on unsupported index type' );
	}

}