<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLTableDefinitionReader;
use Wikibase\Database\QueryInterface\ResultIterator;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @covers Wikibase\Database\MySQL\MySQLTableDefinitionReader
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MySQLTableDefinitionReaderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$this->newInstance();
		$this->assertTrue( true );
	}

	protected function newInstance( $results = array(), $tableExists = true ) {
		$queryInterface = $this
			->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->any() )
			->method( 'tableExists' )
			->will( $this->returnValue( $tableExists ) );

		foreach( $results as $key => $result ){
			$queryInterface->expects( $this->at( $key + 1 ) )
				->method( 'select' )
				->will( $this->returnValue( new ResultIterator( $result ) ) );
		}

		$tableNameFormatter = $this->getMock( 'Wikibase\Database\TableNameFormatter' );

		$tableNameFormatter->expects( $this->any() )
			->method( 'formatTableName' )
			->will( $this->returnCallback( function( $tableName ) {
				return '|' . $tableName . '|';
			} ) );

		return new MySQLTableDefinitionReader( $queryInterface, $tableNameFormatter );
	}

	public function testReadNonExistentTable(){
		$this->setExpectedException( 'Wikibase\Database\Schema\SchemaReadException' );
		$reader = $this->newInstance( array(), false );
		$reader->readDefinition( 'dbNametableName' );
	}

	/**
	 * @dataProvider sqlAndDefinitionProvider
	 */
	public function testReadDefinition( $results, TableDefinition $expectedDefinition ) {
		$reader = $this->newInstance( $results );
		$definition = $reader->readDefinition( 'dbNametableName' );
		$this->assertEquals( $definition, $expectedDefinition );
	}

	public function sqlAndDefinitionProvider() {
		$argLists = array();

		$argLists[] = array(
			array(
				array(
					(object)array( 'name' => 'primaryField', 'type' => 'INT', 'cannull' => 'NO', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'textField', 'type' => 'BLOB', 'cannull' => 'YES', 'defaultvalue' => 'foo', 'extra' => '' ),
					(object)array( 'name' => 'intField', 'type' => 'INT', 'cannull' => 'NO', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'boolField', 'type' => 'TINYINT', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'floatField', 'type' => 'FLOAT', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
				),
				array(
					(object)array( 'name' => 'PRIMARY', 'columnName' => 'intField' ),
					(object)array( 'name' => 'uniqueIndexName', 'columnName' => 'floatField' ),
					(object)array( 'name' => 'uniqueIndexName', 'columnName' => 'boolField' ),
				),
				array( (object)array( 'name' => 'indexName', 'columns' => 'intField,textField' ) )
			),
			new TableDefinition(
				'dbNametableName',
				array(
					new FieldDefinition(
						'primaryField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL,
						FieldDefinition::NO_DEFAULT
					),
					new FieldDefinition(
						'textField',
						FieldDefinition::TYPE_TEXT,
						FieldDefinition::NULL,
						'foo'
					),
					new FieldDefinition(
						'intField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'boolField',
						FieldDefinition::TYPE_BOOLEAN
					),
					new FieldDefinition(
						'floatField',
						FieldDefinition::TYPE_FLOAT
					)
				),
				array(
					new IndexDefinition(
						'uniqueIndexName',
						array( 'floatField' => 0, 'boolField' => 0 ),
						IndexDefinition::TYPE_UNIQUE
					),
					new IndexDefinition(
						'PRIMARY',
						array( 'intField' => 0 ),
						IndexDefinition::TYPE_PRIMARY
					),
					new IndexDefinition(
						'indexName',
						array( 'intField' => 0, 'textField' => 0 ),
						IndexDefinition::TYPE_INDEX
					),
				)
			)
		);

		return $argLists;
	}

}