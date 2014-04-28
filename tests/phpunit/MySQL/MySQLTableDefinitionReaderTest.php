<?php

namespace Wikibase\Database\Tests\MySQL;

use ArrayIterator;
use Wikibase\Database\MySQL\MySQLTableDefinitionReader;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter;

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

	public function testReadNonExistentTable(){
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->any() )
			->method( 'select' )
			->will( $this->throwException(
				$this->getMockBuilder( 'Wikibase\Database\QueryInterface\SelectFailedException' )
					->disableOriginalConstructor()->getMock()
			) );

		$tableNameFormatter = new FakeTableNameFormatter();
		$reader = new MySQLTableDefinitionReader( $queryInterface, $tableNameFormatter );

		$this->setExpectedException( 'Wikibase\Database\Schema\SchemaReadingException' );
		$reader->readDefinition( 'dbNametableName' );
	}

	/**
	 * @dataProvider sqlAndDefinitionProvider
	 */
	public function testReadDefinition( $results, TableDefinition $expectedDefinition ) {
		$reader = $this->newInstance( $results );
		$definition = $reader->readDefinition( 'dbNametableName' );
		$this->assertEquals( $expectedDefinition, $definition );
	}

	private function newInstance( $results = array() ) {
		$queryInterface = $this
			->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		foreach( $results as $key => $result ){
			$queryInterface->expects( $this->at( $key ) )
				->method( 'select' )
				->will( $this->returnValue( new ArrayIterator( $result ) ) );
		}

		$tableNameFormatter = new FakeTableNameFormatter();

		return new MySQLTableDefinitionReader( $queryInterface, $tableNameFormatter );
	}

	public function sqlAndDefinitionProvider() {
		$argLists = array();

		$argLists[] = array(
			array(
				array(
					(object)array( 'name' => 'primaryField', 'type' => 'INT', 'cannull' => 'NO', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'textField', 'type' => 'BLOB', 'cannull' => 'YES', 'defaultvalue' => 'foo', 'extra' => '' ),
					(object)array( 'name' => 'intField', 'type' => 'INT', 'cannull' => 'NO', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'decimalField', 'type' => 'DECIMAL', 'cannull' => 'NO', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'tinyintField', 'type' => 'TINYINT', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'floatField', 'type' => 'FLOAT', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'varcharField', 'type' => 'VARCHAR(255)', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
				),
				array(
					(object)array( 'name' => 'PRIMARY', 'columnName' => 'intField', 'subPart' => null ),
					(object)array( 'name' => 'uniqueIndexName', 'columnName' => 'floatField', 'subPart' => null ),
					(object)array( 'name' => 'uniqueIndexName', 'columnName' => 'tinyintField', 'subPart' => null ),
				),
				array(
					(object)array( 'indexName' => 'indexName', 'colName' => 'intField', 'subPart' => null ),
					(object)array( 'indexName' => 'indexName', 'colName' => 'textField', 'subPart' => 10 ),
				)
			),
			new TableDefinition(
				'dbNametableName',
				array(
					new FieldDefinition(
						'primaryField',
						new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
						FieldDefinition::NOT_NULL,
						FieldDefinition::NO_DEFAULT
					),
					new FieldDefinition(
						'textField',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NULL,
						'foo'
					),
					new FieldDefinition(
						'intField',
						new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'decimalField',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'tinyintField',
						new TypeDefinition( TypeDefinition::TYPE_TINYINT )
					),
					new FieldDefinition(
						'floatField',
						new TypeDefinition( TypeDefinition::TYPE_FLOAT )
					),
					new FieldDefinition(
						'varcharField',
						new TypeDefinition( TypeDefinition::TYPE_VARCHAR, 255 )
					)
				),
				array(
					new IndexDefinition(
						'uniqueIndexName',
						array( 'floatField' , 'tinyintField' ),
						IndexDefinition::TYPE_UNIQUE
					),
					new IndexDefinition(
						'PRIMARY',
						array( 'intField' ),
						IndexDefinition::TYPE_PRIMARY
					),
					new IndexDefinition(
						'indexName',
						array( 'intField' , 'textField' ),
						IndexDefinition::TYPE_INDEX
					),
				)
			)
		);

		return $argLists;
	}

}