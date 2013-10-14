<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MediaWiki\MediaWikiQueryInterface;
use Wikibase\Database\MediaWiki\MediaWikiSchemaModifier;
use Wikibase\Database\MediaWiki\MediaWikiSchemaModifierBuilder;
use Wikibase\Database\MediaWiki\MWTableBuilderBuilder;
use Wikibase\Database\MediaWiki\MWTableDefinitionReaderBuilder;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableBuilder;

/**
 * @since 0.1
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Integration
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class TableCreateModifyDeleteTest extends \PHPUnit_Framework_TestCase {

	protected function tearDown() {
		parent::tearDown();

		$usedTables = array( 'modify_table_test', 'modify_table_test_tmp' );
		$this->dropTablesIfStillThere( $usedTables );
	}

	protected function dropTablesIfStillThere( $tablesToDrop ) {
		$tableBuilder = $this->newTableBuilder();
		foreach( $tablesToDrop as $tableName ){
			if ( $tableBuilder->tableExists( $tableName ) ) {
				$tableBuilder->dropTable( $tableName );
			}
		}
	}

	protected function newTableBuilder() {
		$connectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		$tbBuilder = new MWTableBuilderBuilder();
		return $tbBuilder->setConnection( $connectionProvider )->getTableBuilder();
	}

	protected function newQueryInterface() {
		$connectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		return new MediaWikiQueryInterface( $connectionProvider );
	}

	protected function newTableReader() {
		$connectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		$trBuilder = new MWTableDefinitionReaderBuilder();
		return $trBuilder->setConnection( $connectionProvider )->getTableDefinitionReader( $this->newQueryInterface() );
	}

	protected function newSchemaModifier(){
		$connectionProvider = new LazyDBConnectionProvider( DB_MASTER );
		$schemaModifierBuilder = new MediaWikiSchemaModifierBuilder();
		return $schemaModifierBuilder
			->setConnection( $connectionProvider )
			->setQueryInterface( $this->newQueryInterface() )
			->getSchemaModifier();
	}

	public function testModifyTable(){
		$tableBuilder = $this->newTableBuilder();
		$table = new TableDefinition(
			'modify_table_test',
			array(
				new FieldDefinition( 'startField', FieldDefinition::TYPE_TEXT )
			)
		);

		$this->assertFalse(
			$tableBuilder->tableExists( $table->getName() ),
			'Table should not exist before creation'
		);

		$tableBuilder->createTable( $table );
		$this->assertTable( $tableBuilder, $table, 'assert table after creation' );

		$schemaModifer = $this->newSchemaModifier();

		//add a new field
		$newField = new FieldDefinition( 'secondField', FieldDefinition::TYPE_INTEGER );
		$schemaModifer->addField( $table->getName(), $newField );
		$table = $table->mutateFields( array_merge( $table->getFields(), array( $newField ) ) );
		$this->assertTable( $tableBuilder, $table, 'assert field added' );

		//remove a new index
		$newIndex = new IndexDefinition( 'indexName', array( 'secondField' => 0 ) );
		$schemaModifer->addIndex( $table->getName(), $newIndex );
		$table = $table->mutateIndexes( array_merge( $table->getIndexes(), array( $newIndex ) ) );
		$this->assertTable( $tableBuilder, $table, 'assert index added' );

		//TODO remove and field
		//TODO remove an index
	}

	protected function assertTable( TableBuilder $tableBuilder, TableDefinition $table, $message = '' ){
		$this->assertTrue(
			$tableBuilder->tableExists( $table->getName() ),
			$message . ' (tableExists)'
		);

		$tableReader = $this->newTableReader();

		$this->assertEquals(
			$table,
			$tableReader->readDefinition( $table->getName() ),
			$message . '(definitionEquals)'
		);
	}

}