<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLFieldSqlBuilder;
use Wikibase\Database\Schema\Definitions\FieldDefinition;

/**
 * @covers Wikibase\Database\MySQL\MySQLFieldSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MySQLFieldSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider fieldAndSqlProvider
	 */
	public function testGetCreateTableSql( FieldDefinition $field, $expectedSQL ) {
		$mockEscaper = $this->getMock( 'Wikibase\Database\Escaper' );

		$mockEscaper->expects( $this->exactly( $field->getDefault() === null ? 0 : 1 ) )
			->method( 'getEscapedValue' )
			->will( $this->returnCallback( function( $value ) {
				return '|' . $value . '|';
			} ) );

		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return '-' . $value . '-';
			} ) );

		$sqlBuilder = new MySQLFieldSqlBuilder( $mockEscaper );

		$actualSQL = $sqlBuilder->getFieldSQL( $field );

		$this->assertEquals( $expectedSQL, $actualSQL );
	}

	public function fieldAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_INTEGER
			),
			'-fieldName- INT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_BIGINT
			),
			'-fieldName- BIGINT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_INTEGER,
				FieldDefinition::NULL,
				FieldDefinition::NO_DEFAULT,
				FieldDefinition::NO_ATTRIB,
				FieldDefinition::AUTOINCREMENT
			),
			'-fieldName- INT NULL AUTO_INCREMENT'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_FLOAT,
				FieldDefinition::NOT_NULL
			),
			'-fieldName- FLOAT NOT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_TINYINT,
				FieldDefinition::NOT_NULL,
				'1'
			),
			'-fieldName- TINYINT DEFAULT |1| NOT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_TEXT,
				FieldDefinition::NOT_NULL,
				'foo'
			),
			'-fieldName- BLOB DEFAULT |foo| NOT NULL'
		);

		return $argLists;
	}

	public function testUnsupportedType() {
		$this->setExpectedException( 'RuntimeException', 'does not support db fields of type' );
		$sqlBuilder = new MySQLFieldSqlBuilder( $this->getMock( 'Wikibase\Database\Escaper' ) );
		$sqlBuilder->getFieldSQL( new FieldDefinition( 'fieldName', 'foobar' ) );
	}

}