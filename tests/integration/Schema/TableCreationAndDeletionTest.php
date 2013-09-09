<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\MediaWiki\MWTableBuilderBuilder;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\Schema\Definitions\TableDefinition;

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
		$tableBuilder = $this->newTableBuilder();

		foreach ( array( 'different_field_types', 'default_field_values', 'not_null_fields' ) as $tableName ) {
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
		$tableBuilder = $this->newTableBuilder();

		$this->assertFalse(
			$tableBuilder->tableExists( $table->getName() ),
			'Table should not exist before creation'
		);

		$tableBuilder->createTable( $table );

		$this->assertTrue(
			$tableBuilder->tableExists( $table->getName() ),
			'Table "' . $table->getName() . '" exists after creation'
		);

		$tableBuilder->dropTable( $table->getName() );

		$this->assertFalse(
			$tableBuilder->tableExists( $table->getName() ),
			'Table should not exist after deletion'
		);
	}

}
