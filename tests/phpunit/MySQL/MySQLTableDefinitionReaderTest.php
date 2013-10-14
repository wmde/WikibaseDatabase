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
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MySQLTableDefinitionReaderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$this->newInstance();
		$this->assertTrue( true );
	}

	protected function newInstance( $results = array(), $tableExists = true ) {
		$mockQueryInterface = $this
			->getMockBuilder( 'Wikibase\Database\MediaWiki\MediaWikiQueryInterface' )
			->disableOriginalConstructor()
			->getMock();

		$mockQueryInterface->expects( $this->any() )
			->method( 'tableExists' )
			->will( $this->returnValue( $tableExists ) );

		foreach( $results as $key => $result ){
			$mockQueryInterface->expects( $this->at( $key + 1 ) )
				->method( 'select' )
				->will( $this->returnValue( new ResultIterator( $result ) ) );
		}

		return new MySQLTableDefinitionReader( $mockQueryInterface );
	}

	public function testReadNonExistentTable(){
		$this->setExpectedException( 'Wikibase\Database\QueryInterface\QueryInterfaceException' );
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

		//TODO test case containing constraints PRIMARY & UNIQUE

		$argLists[] = array(
			array(
				array(
					(object)array( 'name' => 'primaryField', 'type' => 'INT', 'cannull' => 'NO', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'textField', 'type' => 'BLOB', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'intField', 'type' => 'INT', 'cannull' => 'NO', 'defaultvalue' => 42, 'extra' => '' ),
					(object)array( 'name' => 'boolField', 'type' => 'TINYINT', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
					(object)array( 'name' => 'floatField', 'type' => 'FLOAT', 'cannull' => 'YES', 'defaultvalue' => null, 'extra' => '' ),
				),
				//TODO test UNIQUE and PRIMARY keys
				array( null ),
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
						FieldDefinition::TYPE_TEXT
					),
					new FieldDefinition(
						'intField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL, 42
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