<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\Database\SQLite\SQLiteSchemaSqlBuilder;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter;

/**
 * @covers Wikibase\Database\SQLite\SQLiteSchemaSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseSQLite
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteSchemaSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	private function newInstance( $existingDefinition = null ) {
		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedValue' )
			->will( $this->returnCallback( function( $value ) {
				return '|' . $value . '|';
			} ) );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return '-' . $value . '-';
			} ) );

		$tableNameFormatter = new FakeTableNameFormatter();

		$mockQueryInterface = $this
			->getMockBuilder( 'Wikibase\Database\SQLite\SQLiteTableDefinitionReader' )
			->disableOriginalConstructor()
			->getMock();
		$mockQueryInterface->expects( $this->any() )
			->method( 'readDefinition' )
			->will( $this->returnValue( $existingDefinition ) );

		return new SQLiteSchemaSqlBuilder( $mockEscaper, $tableNameFormatter, $mockQueryInterface );
	}

	public function testGetRemoveFieldSql() {
		$existingDefinition = new TableDefinition( 'tableName',
			array(
				new FieldDefinition( 'primaryField',
					new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
					FieldDefinition::NOT_NULL,
					FieldDefinition::NO_DEFAULT
				),
				new FieldDefinition( 'textField',
					new TypeDefinition( TypeDefinition::TYPE_BLOB )
				),
				new FieldDefinition( 'intField',
					new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
					FieldDefinition::NOT_NULL, 42
				),
			),
			array(
				new IndexDefinition( 'INDEX',
					array( 'intField', 'primaryField' ),
					IndexDefinition::TYPE_INDEX
				),
			)
		);

		$instance = $this->newInstance( $existingDefinition );
		$sql = $instance->getRemoveFieldSql( 'tableName', 'textField' );
		$this->assertEquals(
			'ALTER TABLE -prefix_tableName- RENAME TO -prefix_tableName_tmp-;' . PHP_EOL
			. 'CREATE TABLE -prefix_tableName- (-primaryField- INTEGER NOT NULL, -intField- INTEGER DEFAULT 42 NOT NULL);' . PHP_EOL
			. 'CREATE INDEX -prefix_tableName-INDEX- ON -prefix_tableName- (-intField-,-primaryField-);' . PHP_EOL
			. 'INSERT INTO -prefix_tableName-(-primaryField-, -intField-) SELECT -primaryField-, -intField- FROM -prefix_tableName_tmp-;' . PHP_EOL
			. 'DROP TABLE -prefix_tableName_tmp-;' ,
			$sql );
	}

	public function testGetAddFieldSql() {
		$instance = $this->newInstance( );
		$sql = $instance->getAddFieldSql( 'tableName', new FieldDefinition( 'intField', new TypeDefinition( TypeDefinition::TYPE_INTEGER ) ) );
		$this->assertEquals( "ALTER TABLE -prefix_tableName- ADD COLUMN -intField- INTEGER NULL", $sql );
	}

	public function testGetRemoveIndexSql() {
		$instance = $this->newInstance( );
		$sql = $instance->getRemoveIndexSql( 'tableName', 'textField' );
		$this->assertEquals( "DROP INDEX IF EXISTS -prefix_tableName-textField-", $sql );
	}

	public function testGetAddIndexSql(){
		$instance = $this->newInstance( );
		$sql = $instance->getAddIndexSql( 'tableName', new IndexDefinition( 'name', array( 'a' , 'b' ), IndexDefinition::TYPE_INDEX ) );
		$this->assertEquals( "CREATE INDEX -prefix_tableName-name- ON -prefix_tableName- (-a-,-b-);", $sql );
	}

}