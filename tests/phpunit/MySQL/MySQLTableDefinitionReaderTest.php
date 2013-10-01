<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\MySQL\MySqlTableDefinitionReader;
use Wikibase\Database\QueryInterface\ResultIterator;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MySQLTableDefinitionReaderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$this->newInstance();
		$this->assertTrue( true );
	}

	protected function newInstance( $results = array() ) {
		$mockQueryInterface = $this
			->getMockBuilder( 'Wikibase\Database\MediaWiki\MediaWikiQueryInterface' )
			->disableOriginalConstructor()
			->getMock();

		$mockQueryInterface->expects( $this->any() )
			->method( 'tableExists' )
			->will( $this->returnValue( true ) );

		foreach( $results as $key => $result ){
			$mockQueryInterface->expects( $this->at( $key + 1 ) )
				->method( 'select' )
				->will( $this->returnValue( new ResultIterator( $result ) ) );
		}

		return new MySqlTableDefinitionReader( $mockQueryInterface );
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
					array( 'name' => 'primaryField', 'type' => 'INT', 'cannull' => 'NO', 'default' => null ),
					array( 'name' => 'textField', 'type' => 'BLOB', 'cannull' => 'YES', 'default' => null ),
					array( 'name' => 'intField', 'type' => 'INT', 'cannull' => 'NO', 'default' => 42 ),
				),
				//TODO test UNIQUE and PRIMARY keys
				array( array( ) ),
				array( array( 'name' => 'indexName', 'columns' => 'intField,textField' ) )
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