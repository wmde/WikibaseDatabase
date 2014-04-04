<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
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

		//Dont expect quoted Ints!
		$fieldTypeName = $field->getType()->getName();
		if( $fieldTypeName === TypeDefinition::TYPE_INTEGER || $fieldTypeName === TypeDefinition::TYPE_BIGINT || $fieldTypeName === TypeDefinition::TYPE_TINYINT ){
			$expectedEscapedValue = 0;
		} else {
			$expectedEscapedValue = $field->getDefault() === null ? 0 : 1;
		}

		$mockEscaper->expects( $this->exactly( $expectedEscapedValue ) )
			->method( 'getEscapedValue' )
			->will( $this->returnCallback( function( $value ) {
				return '|' . $value . '|';
			} ) );
		$mockEscaper->expects( $this->any() )
			->method( 'getEscapedIdentifier' )
			->will( $this->returnCallback( function( $value ) {
				return '-' . $value . '-';
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
				new TypeDefinition( TypeDefinition::TYPE_TINYINT )
			),
			'-fieldName- TINYINT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'autoInc',
				new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
				FieldDefinition::NOT_NULL,
				FieldDefinition::NO_DEFAULT,
				FieldDefinition::AUTOINCREMENT

			),
			'-autoInc- INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT'
		);

		$argLists[] = array(
			new FieldDefinition(
				'autoInc',
				new TypeDefinition( TypeDefinition::TYPE_BIGINT ),
				FieldDefinition::NOT_NULL,
				FieldDefinition::NO_DEFAULT,
				FieldDefinition::AUTOINCREMENT

			),
			'-autoInc- BIGINT NOT NULL PRIMARY KEY AUTOINCREMENT'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				new TypeDefinition( TypeDefinition::TYPE_TINYINT ),
				FieldDefinition::NOT_NULL,
				'1'
			),
			'-fieldName- TINYINT DEFAULT 1 NOT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				new TypeDefinition( TypeDefinition::TYPE_BLOB ),
				FieldDefinition::NOT_NULL,
				'foo'
			),
			'-fieldName- BLOB DEFAULT |foo| NOT NULL'
		);

		$argLists[] = array(
			new FieldDefinition(
				'fieldName',
				new TypeDefinition( TypeDefinition::TYPE_VARCHAR, 255 ),
				FieldDefinition::NOT_NULL,
				'foo'
			),
			'-fieldName- VARCHAR(255) DEFAULT |foo| NOT NULL'
		);

		return $argLists;
	}

}