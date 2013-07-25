<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\FieldDefinition;
use Wikibase\Database\TableBuilder;
use Wikibase\Database\TableDefinition;

/**
 * @covers Wikibase\Database\TableBuilder
 *
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
class TableBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider tableNameProvider
	 */
	public function testCreateTableCallsTableExists( $tableName ) {
		$table = new TableDefinition(
			$tableName,
			array( new FieldDefinition( 'foo', FieldDefinition::TYPE_TEXT ) )
		);

		$reporter = $this->getMock( 'Wikibase\Database\MessageReporter' );

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$queryInterface->expects( $this->once() )
			->method( 'tableExists' )
			->with( $table->getName() );

		$builder = new TableBuilder( $queryInterface, $reporter );

		$builder->createTable( $table );
	}

	public function tableNameProvider() {
		return array(
			array( 'foo' ),
			array( 'bar' ),
			array( 'o' ),
			array( 'foo_bar_baz' ),
			array( 'foobarbaz ' ),
		);
	}

}
