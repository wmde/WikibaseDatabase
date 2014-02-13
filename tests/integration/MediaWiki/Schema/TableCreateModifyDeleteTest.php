<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MediaWiki\MediaWikiQueryInterface;
use Wikibase\Database\MediaWiki\MediaWikiSchemaModifierBuilder;
use Wikibase\Database\MediaWiki\MWTableBuilderBuilder;
use Wikibase\Database\MediaWiki\MWTableDefinitionReaderBuilder;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @since 0.1
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Integration
 * @group Database
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
		foreach( $tablesToDrop as $tableName ) {
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
		return $trBuilder
			->setConnection( $connectionProvider )
			->setQueryInterface( $this->newQueryInterface() )
			->getTableDefinitionReader();
	}

	protected function newSchemaModifier() {
		$connectionProvider = new LazyDBConnectionProvider( DB_MASTER );
		$schemaModifierBuilder = new MediaWikiSchemaModifierBuilder();
		return $schemaModifierBuilder
			->setConnection( $connectionProvider )
			->setQueryInterface( $this->newQueryInterface() )
			->getSchemaModifier();
	}

	public function getType(){
		$connectionProvider = new LazyDBConnectionProvider( DB_MASTER );
		return $connectionProvider->getConnection()->getType();
	}

	public function testAddField() {
		$table = new TableDefinition(
			'modify_table_test',
			array(
				new FieldDefinition( 'startField', FieldDefinition::TYPE_BLOB )
			)
		);
		$this->setupTestTable( $table );

		$newField = new FieldDefinition( 'secondField', FieldDefinition::TYPE_INTEGER );
		$this->newSchemaModifier()->addField( $table->getName(), $newField );
		$table = $table->mutateFields( array_merge( $table->getFields(), array( $newField ) ) );
		$this->assertTableExistsAsDefined( $table, 'assert field added' );
	}

	public function testAddIndex() {
		$table = new TableDefinition(
			'modify_table_test',
			array(
				new FieldDefinition( 'startField', FieldDefinition::TYPE_INTEGER )
			)
		);
		$this->setupTestTable( $table );

		$newIndex = new IndexDefinition( 'indexName', array( 'startField' => 0 ) );
		$this->newSchemaModifier()->addIndex( $table->getName(), $newIndex );
		$table = $table->mutateIndexes( array_merge( $table->getIndexes(), array( $newIndex ) ) );
		$this->assertTableExistsAsDefined( $table, 'assert index added' );
	}

	public function testRemoveField() {
		$table = new TableDefinition(
			'modify_table_test',
			array(
				new FieldDefinition( 'startField1', FieldDefinition::TYPE_BLOB ),
				new FieldDefinition( 'startField2', FieldDefinition::TYPE_BLOB ),
			)
		);
		$this->setupTestTable( $table );

		$removeField = new FieldDefinition( 'startField2', FieldDefinition::TYPE_INTEGER );
		$this->newSchemaModifier()->removeField( $table->getName(), $removeField->getName() );
		$table = $table->mutateFieldAway( $removeField->getName() );
		$this->assertTableExistsAsDefined( $table, 'assert field removed' );
	}

	public function testRemoveIndex() {
		$table = new TableDefinition(
			'modify_table_test',
			array(
				new FieldDefinition( 'startField', FieldDefinition::TYPE_INTEGER )
			),
			array(
				new IndexDefinition( 'indexName', array( 'startField' => 0 ) )
			)
		);
		$this->setupTestTable( $table );

		$removeIndex = new IndexDefinition( 'indexName', array( 'startField' => 0 ) );
		$this->newSchemaModifier()->removeIndex( $table->getName(), $removeIndex->getName() );
		$table = $table->mutateIndexAway( $removeIndex->getName() );
		$this->assertTableExistsAsDefined( $table, 'assert index removed' );
	}

	public function testFieldAddRemoveRoundtrip() {
		$startTable = new TableDefinition(
			'modify_table_test',
			array(
				new FieldDefinition( 'startField', FieldDefinition::TYPE_BLOB )
			)
		);
		$this->setupTestTable( $startTable );
		$field = new FieldDefinition( 'secondField', FieldDefinition::TYPE_INTEGER );

		$this->newSchemaModifier()->addField( $startTable->getName(), $field );
		$newTable = $startTable->mutateFields( array_merge( $startTable->getFields(), array( $field ) ) );
		$this->assertTableExistsAsDefined( $newTable, 'assert field added' );

		$this->newSchemaModifier()->removeField( $startTable->getName(), $field->getName() );
		$this->assertTableExistsAsDefined( $startTable, 'assert field remove' );
	}

	public function testIndexAddRemoveRoundtrip() {
		$startTable = new TableDefinition(
			'modify_table_test',
			array(
				new FieldDefinition( 'startField', FieldDefinition::TYPE_INTEGER )
			)
		);
		$this->setupTestTable( $startTable );
		$index = new IndexDefinition( 'indexName', array( 'startField' => 0 ) );

		$this->newSchemaModifier()->addIndex( $startTable->getName(), $index );
		$newTable = $startTable->mutateIndexes( array_merge( $startTable->getIndexes(), array( $index ) ) );
		$this->assertTableExistsAsDefined( $newTable, 'assert index added' );

		$this->newSchemaModifier()->removeIndex( $startTable->getName(), $index->getName() );
		$this->assertTableExistsAsDefined( $startTable, 'assert index remove' );
	}

	public function setupTestTable( TableDefinition $table ) {
		$tableBuilder = $this->newTableBuilder();
		$this->assertFalse(
			$tableBuilder->tableExists( $table->getName() ),
			'Table should not exist before creation'
		);

		$tableBuilder->createTable( $table );
		$this->assertTableExistsAsDefined( $table, 'assert table after creation' );
	}

	protected function assertTableExistsAsDefined( TableDefinition $expectedTable, $message = '' ) {
		$this->assertTrue(
			$this->newTableBuilder()->tableExists( $expectedTable->getName() ),
			$message . ' (tableExists)'
		);

		$actualTable = $this->newTableReader()->readDefinition( $expectedTable->getName() );

		$this->assertEquals(
			$expectedTable,
			$actualTable,
			$message . ' (definitionEquals)'
		);
	}

}
