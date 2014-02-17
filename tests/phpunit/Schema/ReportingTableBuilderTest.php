<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\Database\Schema\ReportingTableBuilder;

/**
 * @covers Wikibase\Database\Schema\ReportingTableBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReportingTableBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider tableProvider
	 */
	public function testCreateTableForNonExistingTable( TableDefinition $table ) {
		$innerBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );
		$reporter = $this->getMock( 'Wikibase\Database\MessageReporter' );

		$innerBuilder->expects( $this->once() )
			->method( 'tableExists' )
			->will( $this->returnValue( false ) );

		$reporter->expects( $this->exactly( 2 ) )
			->method( 'reportMessage' );

		$innerBuilder->expects( $this->once() )
			->method( 'createTable' )
			->with( $this->equalTo( $table ) );

		$reportingBuilder = new ReportingTableBuilder( $innerBuilder, $reporter );

		$reportingBuilder->createTable( $table );
	}

	public function tableProvider() {
		$tables = array();

		$tables[] = new TableDefinition( 'differentfieldtypes', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ) ),
			new FieldDefinition( 'floatfield', new TypeDefinition( TypeDefinition::TYPE_FLOAT ) ),
			new FieldDefinition( 'textfield', new TypeDefinition( TypeDefinition::TYPE_BLOB ) ),
			new FieldDefinition( 'tinyintfield', new TypeDefinition( TypeDefinition::TYPE_TINYINT ) ),
		) );

		$tables[] = new TableDefinition( 'defaultfieldvalues', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ), true, 42 ),
		) );

		$tables[] = new TableDefinition( 'defaultfieldvalues', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ), true, 42 ),
			new FieldDefinition( 'bigintfield', new TypeDefinition( TypeDefinition::TYPE_BIGINT ), true, 42 ),
			new FieldDefinition( 'decimalfield', new TypeDefinition( TypeDefinition::TYPE_DECIMAL ) ),
		) );

		$tables[] = new TableDefinition( 'notnullfields', array(
			new FieldDefinition( 'intfield', new TypeDefinition( TypeDefinition::TYPE_INTEGER ), false ),
			new FieldDefinition( 'textfield', new TypeDefinition( TypeDefinition::TYPE_BLOB ), false ),
		) );

		$argLists = array();

		foreach ( $tables as $table ) {
			$argLists[] = array( $table );
		}

		return $argLists;
	}

	/**
	 * @dataProvider tableProvider
	 */
	public function testCreateTableForExistingTable( TableDefinition $table ) {
		$innerBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );
		$reporter = $this->getMock( 'Wikibase\Database\MessageReporter' );

		$innerBuilder->expects( $this->once() )
			->method( 'tableExists' )
			->will( $this->returnValue( true ) );

		$innerBuilder->expects( $this->never() )
			->method( 'createTable' );

		$reporter->expects( $this->exactly( 1 ) )
			->method( 'reportMessage' );

		$reportingBuilder = new ReportingTableBuilder( $innerBuilder, $reporter );

		$reportingBuilder->createTable( $table );
	}

	/**
	 * @dataProvider tableProvider
	 */
	public function testDeleteExistingTable( TableDefinition $table ) {
		$innerBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );
		$reporter = $this->getMock( 'Wikibase\Database\MessageReporter' );

		$innerBuilder->expects( $this->once() )
			->method( 'tableExists' )
			->will( $this->returnValue( true ) );

		$innerBuilder->expects( $this->once() )
			->method( 'dropTable' )
			->with( $this->equalTo( $table->getName() ) );

		$reporter->expects( $this->exactly( 2 ) )
			->method( 'reportMessage' );

		$reportingBuilder = new ReportingTableBuilder( $innerBuilder, $reporter );

		$reportingBuilder->dropTable( $table->getName() );
	}

	/**
	 * @dataProvider tableProvider
	 */
	public function testDeleteNonExistingTable( TableDefinition $table ) {
		$innerBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );
		$reporter = $this->getMock( 'Wikibase\Database\MessageReporter' );

		$innerBuilder->expects( $this->once() )
			->method( 'tableExists' )
			->will( $this->returnValue( false ) );

		$innerBuilder->expects( $this->never() )
			->method( 'dropTable' );

		$reporter->expects( $this->exactly( 1 ) )
			->method( 'reportMessage' );

		$reportingBuilder = new ReportingTableBuilder( $innerBuilder, $reporter );

		$reportingBuilder->dropTable( $table->getName() );
	}

	/**
	 * @dataProvider tableExistsProvider
	 */
	public function testTableExists( $tableExists ){
		$innerBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );
		$reporter = $this->getMock( 'Wikibase\Database\MessageReporter' );

		$innerBuilder->expects( $this->once() )
			->method( 'tableExists' )
			->will( $this->returnValue( $tableExists ) );

		$reportingBuilder = new ReportingTableBuilder( $innerBuilder, $reporter );
		$this->assertEquals( $tableExists, $reportingBuilder->tableExists( 'foo' ) );
	}

	public function tableExistsProvider(){
		return array(
			array( true ),
			array( false )
		);
	}
}
