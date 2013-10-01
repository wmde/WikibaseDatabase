<?php

namespace Wikibase\Database\Tests\MySQL;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\SQLite\SQliteFieldSqlBuilder;

/**
 * @covers Wikibase\Database\SQLite\SQLiteFieldSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQliteFieldSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	private function newInstance() {
		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedValue' )
			->will( $this->returnArgument(0) );

		return new SQliteFieldSqlBuilder( $mockEscaper );
	}

	/**
	 * @dataProvider fieldAndSqlProvider
	 */
	public function testGetCreateTableSql( FieldDefinition $field, $expectedSQL ) {
		$sqlBuilder = $this->newInstance();

		$actualSQL = $sqlBuilder->getFieldSQL( $field );

		$this->assertEquals( $expectedSQL, $actualSQL );
	}

	public function fieldAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new FieldDefinition( 'fieldName', 'bool' ),
			'fieldName TINYINT NULL'
		);

		$argLists[] = array(
			new FieldDefinition( 'fieldName', 'bool', false, '1' ),
			'fieldName TINYINT DEFAULT 1 NOT NULL'
		);

		$argLists[] = array(
			new FieldDefinition( 'fieldName', 'str', false, 'foo' ),
			'fieldName BLOB DEFAULT foo NOT NULL'
		);

		return $argLists;
	}

}