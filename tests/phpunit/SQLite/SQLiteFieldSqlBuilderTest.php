<?php

namespace Wikibase\Database\Tests\SQLite;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\SQLite\SQLiteFieldSqlBuilder;

/**
 * @covers Wikibase\Database\SQLite\SQLiteFieldSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseSQLite
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteFieldSqlBuilderTest extends \PHPUnit_Framework_TestCase {

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

		$sqlBuilder = new SQLiteFieldSqlBuilder( $mockEscaper );

		$actualSQL = $sqlBuilder->getFieldSQL( $field );

		$this->assertEquals( $expectedSQL, $actualSQL );
	}

	public function fieldAndSqlProvider() {
		$argLists = array();

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_BOOLEAN
			),
			'fieldName TINYINT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'autoInc',
				FieldDefinition::TYPE_INTEGER,
				FieldDefinition::NOT_NULL,
				FieldDefinition::NO_DEFAULT,
				FieldDefinition::NO_ATTRIB,
				FieldDefinition::AUTOINCREMENT

			),
			'autoInc INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_BOOLEAN,
				FieldDefinition::NOT_NULL,
				'1'
			),
			'fieldName TINYINT DEFAULT |1| NOT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				FieldDefinition::TYPE_TEXT,
				FieldDefinition::NOT_NULL,
				'foo'
			),
			'fieldName BLOB DEFAULT |foo| NOT NULL'
		);

		return $argLists;
	}

	public function testUnsupportedType() {
		$this->setExpectedException( 'RuntimeException', 'does not support db fields of type' );
		$sqlBuilder = new SQLiteFieldSqlBuilder( $this->getMock( 'Wikibase\Database\Escaper' ) );
		$sqlBuilder->getFieldSQL( new FieldDefinition( 'fieldName', 'foobar' ) );
	}

}