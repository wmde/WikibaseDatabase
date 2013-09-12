<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
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
			->method( 'removeField' );

		$schema->expects( $this->never() )
			->method( 'addField' );

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
					FieldDefinition::TYPE_BOOLEAN
				)
			)
		);

		$definitions[] = new TableDefinition(
			'foo',
			array(
				new FieldDefinition(
					'bool',
					FieldDefinition::TYPE_BOOLEAN
				),
				new FieldDefinition(
					'int',
					FieldDefinition::TYPE_INTEGER
				),
				new FieldDefinition(
					'text',
					FieldDefinition::TYPE_TEXT
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

}
