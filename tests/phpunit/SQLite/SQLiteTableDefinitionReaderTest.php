<?php

namespace Wikibase\Database\Tests\SQLite;

use ArrayIterator;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\SQLite\SQLiteTableDefinitionReader;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter;

/**
 * @covers Wikibase\Database\SQLite\SQLiteTableDefinitionReader
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseSQLite
 * @group Database
 *
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

		$mockUnEscaper = $this->getMock( 'Wikibase\Database\SQLite\SQLiteUnEscaper' );
		$mockUnEscaper->expects( $this->any() )
			->method( 'getUnEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return substr( $value, 1, -1 );
			} ) );

		$mockTableNameFormatter = new FakeTableNameFormatter();

		foreach( $results as $key => $result ){
			$mockQueryInterface->expects( $this->at( $key + 1 ) )
				->method( 'select' )
				->will( $this->returnValue( new ArrayIterator( $result ) ) );
		}

		return new SQLiteTableDefinitionReader(
			$mockQueryInterface,
			$mockUnEscaper,
			$mockTableNameFormatter
		);
	}

	public function testReadNonExistentTable(){
		$this->setExpectedException( 'Wikibase\Database\Schema\SchemaReadingException' );
		$reader = $this->newInstance( array(), false );
		$reader->readDefinition( 'fooBarImNotATable' );
	}

	/**
	 * @dataProvider sqlAndDefinitionProvider
	 */
	public function testReadDefinition( $results, TableDefinition $expectedDefinition ) {
		$reader = $this->newInstance( $results );
		$readDefinition = $reader->readDefinition( $expectedDefinition->getName() );
		$this->assertEquals( $expectedDefinition, $readDefinition );
	}

	public function sqlAndDefinitionProvider() {
		$argLists = array();

		$argLists[] = array(
			array(
				array( (object)array( 'sql' => 'CREATE TABLE underscore_name ("startField" BLOB NULL )' ) ),
				array(),
				array(),
			),
			new TableDefinition(
				'underscore_name',
				array(
					new FieldDefinition( 'startField', FieldDefinition::TYPE_TEXT )
				)
			),
		);

		$argLists[] = array(
			array(
				//create sql
				array( (object)array( 'sql' => 'CREATE TABLE dbNametableName ("primaryField" INT NOT NULL, "textField" BLOB NULL, "intField" INT DEFAULT 42 NOT NULL, PRIMARY KEY ("textField", "primaryField"))' ) ),
				//indexes sql
				array(
					(object)array( 'sql' => 'CREATE UNIQUE INDEX "uniqueName" ON dbNametableName ("textField")' ),
					(object)array( 'sql' => 'CREATE INDEX "indexName" ON dbNametableName ("intField","textField")' )
				),
				//primarykey sql
				array( (object)array( 'sql' => 'PRIMARY KEY ("textField","primaryField")' ) ),
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
						'uniqueName',
						array( 'textField' => 0 ),
						IndexDefinition::TYPE_UNIQUE
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

	/**
	 * @dataProvider incorrectSqlCountProvider
	 */
	public function testExceptionOnWrongNumberOfResults( $results ) {
		$this->setExpectedException( 'Wikibase\Database\Schema\SchemaReadingException' );
		$reader = $this->newInstance( $results );
		$reader->readDefinition( 'foo' );
	}

	public function incorrectSqlCountProvider() {
		$argLists = array();

		$argLists['0 results'] = array( array( array(  ) ) );
		$argLists['2 results'] = array( array( array( (object)array( 'sql' => '' ), (object)array( 'sql' => '' ) ) ) );

		return $argLists;
	}

}
