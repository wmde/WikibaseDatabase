<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\FieldDefinition;
use Wikibase\Database\IndexDefinition;
use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MediaWiki\MWQueryInterfaceBuilder;
use Wikibase\Database\TableDefinition;

/**
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseDatabaseTest
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableCreationAndDeletionTest extends \PHPUnit_Framework_TestCase {

	protected function tearDown() {
		parent::tearDown();

		$this->dropTablesIfStillThere();
	}

	protected function dropTablesIfStillThere() {
		$queryInterface = $this->newQueryInterface();

		foreach ( array( 'different_field_types', 'default_field_values', 'not_null_fields' ) as $tableName ) {
			if ( $queryInterface->tableExists( $tableName ) ) {
				$queryInterface->dropTable( $tableName );
			}
		}
	}

	protected function newQueryInterface() {
		$connectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		$qiBuilder = new MWQueryInterfaceBuilder();
		return $qiBuilder->setConnection( $connectionProvider )->getQueryInterface();
	}

	public function tableProvider() {
		$tables = array();

		$tables[] = new TableDefinition( 'different_field_types', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER ),
			new FieldDefinition( 'floatfield', FieldDefinition::TYPE_FLOAT ),
			new FieldDefinition( 'textfield', FieldDefinition::TYPE_TEXT ),
			new FieldDefinition( 'boolfield', FieldDefinition::TYPE_BOOLEAN ),
		) );

		$tables[] = new TableDefinition( 'default_field_values', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER, true, 42 ),
		) );

		$tables[] = new TableDefinition( 'not_null_fields', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER, false ),
			new FieldDefinition( 'textfield', FieldDefinition::TYPE_TEXT, false ),
		) );

		$tables[] = new TableDefinition( 'not_null_fields', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER, false ),
			new FieldDefinition( 'textfield', FieldDefinition::TYPE_TEXT, false ),
		) );

		$tables[] = new TableDefinition( 'default_field_values', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER, false ),
			new FieldDefinition( 'floatfield', FieldDefinition::TYPE_FLOAT, false ),
			new FieldDefinition( 'boolfield', FieldDefinition::TYPE_BOOLEAN, false ),
			),
			array( new IndexDefinition( 'somename', array( 'intfield' => 0, 'floatfield' => 0 ) ) )
		);

		$argLists = array();

		foreach ( $tables as $table ) {
			$argLists[] = array( $table );
		}

		return $argLists;
	}

	/**
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testCreateAndDropTable( TableDefinition $table ) {
		$queryInterface = $this->newQueryInterface();

		$this->assertFalse(
			$queryInterface->tableExists( $table->getName() ),
			'Table should not exist before creation'
		);

		$queryInterface->createTable( $table );

		$this->assertTrue(
			$queryInterface->tableExists( $table->getName() ),
			'Table "' . $table->getName() . '" exists after creation'
		);

		$this->assertTrue(
			$queryInterface->dropTable( $table->getName() ),
			'Table removal worked'
		);

		$this->assertFalse(
			$queryInterface->tableExists( $table->getName() ),
			'Table should not exist after deletion'
		);
	}

}
