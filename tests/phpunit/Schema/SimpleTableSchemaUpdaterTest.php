<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\FieldRemovalFailedException;
use Wikibase\Database\Schema\SimpleTableSchemaUpdater;

/**
 * @covers Wikibase\Database\Schema\SimpleTableSchemaUpdater
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SimpleTableSchemaUpdaterTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new SimpleTableSchemaUpdater( $this->getMock( 'Wikibase\Database\Schema\SchemaModifier' ) );
		$this->assertTrue( true );
	}

	/**
	 * @dataProvider tableDefinitionProvider
	 */
	public function testNoUpdatesCausedBySameDefinition( TableDefinition $tableDefinition ) {
		$schema = $this->getMock( 'Wikibase\Database\Schema\SchemaModifier' );

		$schema->expects( $this->never() )
			->method( $this->anything() );

		$updater = new SimpleTableSchemaUpdater( $schema );

		$updater->updateTable( $tableDefinition, $tableDefinition );
	}

	public function tableDefinitionProvider() {
		$definitions = array();

		$definitions[] = new TableDefinition(
			'foo',
			array(
				new FieldDefinition(
					'field',
					FieldDefinition::TYPE_TINYINT
				)
			)
		);

		$definitions[] = new TableDefinition(
			'foo',
			array(
				new FieldDefinition(
					'field',
					FieldDefinition::TYPE_TINYINT
				),
				new FieldDefinition(
					'field2',
					FieldDefinition::TYPE_BIGINT
				),
				new FieldDefinition(
					'field3',
					FieldDefinition::TYPE_DECIMAL
				),
			)
		);

		$definitions[] = new TableDefinition(
			'foo',
			array(
				new FieldDefinition(
					'tinyint',
					FieldDefinition::TYPE_TINYINT
				),
				new FieldDefinition(
					'int',
					FieldDefinition::TYPE_INTEGER
				),
				new FieldDefinition(
					'text',
					FieldDefinition::TYPE_BLOB
				)
			),
			array(
				new IndexDefinition(
					'some_index',
					array(
						'int' => 0,
						'text' => 10,
					)
				),
				new IndexDefinition(
					'other_index',
					array(
						'text' => 0,
					),
					IndexDefinition::TYPE_UNIQUE
				)
			)
		);

		return array_map( function( $definition ) { return array( $definition ); }, $definitions );
	}

	/**
	 * @dataProvider tableDefinitionProvider
	 */
	public function testNewFieldsGetAdded( TableDefinition $tableDefinition ) {
		$schema = $this->getMock( 'Wikibase\Database\Schema\SchemaModifier' );

		$fields = $tableDefinition->getFields();

		$schema->expects( $this->exactly( count( $fields ) - 1 ) )
			->method( 'addField' );

		$schema->expects( $this->never() )
			->method( $this->logicalNot( $this->equalTo( 'addField' ) ) );

		$updater = new SimpleTableSchemaUpdater( $schema );

		$updater->updateTable(
			$tableDefinition->mutateFields( array( reset( $fields ) ) ),
			$tableDefinition
		);
	}

	/**
	 * @dataProvider tableDefinitionProvider
	 */
	public function testRemovedFieldsGetRemoved( TableDefinition $tableDefinition ) {
		$schema = $this->getMock( 'Wikibase\Database\Schema\SchemaModifier' );

		$fields = $tableDefinition->getFields();

		$schema->expects( $this->exactly( count( $fields ) - 1 ) )
			->method( 'removeField' );

		$schema->expects( $this->never() )
			->method( $this->logicalNot( $this->equalTo( 'removeField' ) ) );

		$updater = new SimpleTableSchemaUpdater( $schema );

		$updater->updateTable(
			$tableDefinition,
			$tableDefinition->mutateFields( array( reset( $fields ) ) )
		);
	}

	/**
	 * @dataProvider tableDefinitionProvider
	 */
	public function testTablesWithDifferentNamesCauseException( TableDefinition $tableDefinition ) {
		$schema = $this->getMock( 'Wikibase\Database\Schema\SchemaModifier' );

		$updater = new SimpleTableSchemaUpdater( $schema );

		$this->setExpectedException( 'Wikibase\Database\Schema\TableSchemaUpdateException' );

		$updater->updateTable(
			$tableDefinition,
			$tableDefinition->mutateName( $tableDefinition->getName() . '_foo' )
		);
	}

	/**
	 * @dataProvider tableDefinitionProvider
	 */
	public function testSchemaModificationExceptionPropagatesCorrectly( TableDefinition $tableDefinition ) {
		$schema = $this->getMock( 'Wikibase\Database\Schema\SchemaModifier' );

		$schema->expects( $this->once() )
			->method( 'removeField' )
			->will( $this->throwException(
				new FieldRemovalFailedException( $tableDefinition->getName(), 'bar' )
			) );

		$updater = new SimpleTableSchemaUpdater( $schema );

		$field = new FieldDefinition( 'rewtwery', FieldDefinition::TYPE_BLOB );

		$this->setExpectedException( 'Wikibase\Database\Schema\TableSchemaUpdateException' );

		$updater->updateTable(
			$tableDefinition,
			$tableDefinition->mutateFields( array( $field ) )
		);
	}

}
