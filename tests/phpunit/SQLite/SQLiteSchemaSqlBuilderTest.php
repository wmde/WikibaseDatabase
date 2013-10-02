<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\SQLite\SQLiteSchemaSqlBuilder;

/**
 * @covers Wikibase\Database\SQLite\SQLiteSchemaSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteSchemaSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	private function newInstance() {
		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedValue' )
			->will( $this->returnArgument(0) );

		return new SQLiteSchemaSqlBuilder( $mockEscaper );
	}

	public function testGetAddFieldSql(){
		$instance = $this->newInstance();
		$field = new FieldDefinition( 'intField', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42 );
		$sql = $instance->getAddFieldSql( 'tableName', $field );
		$this->assertEquals( 'ALTER TABLE tableName ADD COLUMN intField INT DEFAULT 42 NOT NULL', $sql );
	}

}