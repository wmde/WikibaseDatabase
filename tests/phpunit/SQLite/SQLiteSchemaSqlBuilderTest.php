<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\SQLite\SQLiteSchemaSqlBuilder;

/**
 * @covers Wikibase\Database\SQLite\SQLiteSchemaSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteSchemaSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	private function newInstance( $existingDefinition ) {
		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedValue' )
			->will( $this->returnArgument(0) );

		$mockTableNameFormatter = $this->getMock( 'Wikibase\Database\TableNameFormatter' );
		$mockTableNameFormatter->expects( $this->atLeastOnce() )
			->method( 'formatTableName' )
			->will( $this->returnArgument(0) );

		$mockQueryInterface = $this
			->getMockBuilder( 'Wikibase\Database\SQLite\SQLiteTableDefinitionReader' )
			->disableOriginalConstructor()
			->getMock();
		$mockQueryInterface->expects( $this->atLeastOnce() )
			->method( 'readDefinition' )
			->will( $this->returnValue( $existingDefinition ) );

		return new SQLiteSchemaSqlBuilder( $mockEscaper, $mockTableNameFormatter, $mockQueryInterface );
	}

	public function testGetAddFieldSql(){
		$existingDefinition = new TableDefinition( 'tableName',
			array(
				new FieldDefinition( 'primaryField',
					FieldDefinition::TYPE_INTEGER,
					FieldDefinition::NOT_NULL,
					FieldDefinition::NO_DEFAULT
				),
				new FieldDefinition( 'textField',
					FieldDefinition::TYPE_TEXT
				),
				new FieldDefinition( 'intField',
					FieldDefinition::TYPE_INTEGER,
					FieldDefinition::NOT_NULL, 42
				),
			),
			array(
				new IndexDefinition( 'INDEX',
					array( 'intField' => 0, 'primaryField' => 0 ),
					IndexDefinition::TYPE_INDEX
				),
			)
		);

		$instance = $this->newInstance( $existingDefinition );
		$sql = $instance->getRemoveFieldSql( 'tableName', 'textField' );
		$this->assertEquals( 'ALTER TABLE tableName RENAME TO tableName_tmp;CREATE TABLE tableName (primaryField INT NOT NULL, intField INT DEFAULT 42 NOT NULL);CREATE INDEX INDEX ON tableName (intField,primaryField);INSERT INTO tableName(primaryField, intField) SELECT primaryField, intField FROM tableName_tmp;DROP TABLE tableName_tmp;', $sql );
	}

}