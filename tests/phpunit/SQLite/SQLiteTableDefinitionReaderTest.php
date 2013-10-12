<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\QueryInterface\ResultIterator;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\SQLite\SQLiteTableDefinitionReader;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteTableDefinitionReaderTest extends \PHPUnit_Framework_TestCase {

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

		return new SQLiteTableDefinitionReader( $mockQueryInterface );
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

		$argLists[] = array(
			array(
				array( (object)array( 'sql' => 'CREATE TABLE dbNametableName (primaryField INT NOT NULL, textField BLOB NULL, intField INT DEFAULT 42 NOT NULL, PRIMARY KEY (textField, primaryField))' ) ),
				array( (object)array( 'sql' => 'CREATE INDEX indexName ON dbNametableName (intField,textField)' ) ),
				array( (object)array( 'sql' => 'CREATE TABLE dbNametableName (primaryField INT NOT NULL, textField BLOB NULL, intField INT DEFAULT 42 NOT NULL, PRIMARY KEY (textField, primaryField))' ) ),
			),
			new TableDefinition(
				'dbNametableName',
				array(
					new FieldDefinition(
						'primaryField',
						FieldDefinition::TYPE_INTEGER,
						FieldDefinition::NOT_NULL,
						FieldDefinition::NO_DEFAULT,
						FieldDefinition::NO_ATTRIB
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
					new IndexDefinition(
						'PRIMARY',
						array( 'textField' => 0, 'primaryField' => 0 ),
						IndexDefinition::TYPE_PRIMARY
					),
				)
			)
		);

		return $argLists;
	}

}