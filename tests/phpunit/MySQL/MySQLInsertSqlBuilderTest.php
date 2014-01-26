<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLInsertSqlBuilder;

/**
 * @covers Wikibase\Database\MySQL\MySQLInsertSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLInsertSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var MySQLInsertSqlBuilder
	 */
	protected $sqlBuilder;

	public function setUp() {
		$escaper = $this->getMock( 'Wikibase\Database\ValueEscaper' );

		$escaper->expects( $this->any() )
			->method( 'getEscapedValue' )
			->will( $this->returnCallback( function( $value ) {
				return '|' . $value . '|';
			} ) );

		$this->sqlBuilder = new MySQLInsertSqlBuilder( $escaper );
	}

	public function testGivenNoValues_returnsEmptyString() {
		$this->assertTableAndValuesResultInSql( 'some_table', array(), '' );
	}

	protected function assertTableAndValuesResultInSql( $tableName, array $values, $sql ) {
		$this->assertSame(
			$sql,
			$this->sqlBuilder->getInsertSql( $tableName, $values )
		);
	}

	public function testInsertOneValueForOneField() {
		$this->assertTableAndValuesResultInSql(
			'some_table',
			array(
				'some_field' => 'foobar'
			),
			"INSERT INTO some_table (some_field) VALUES (|foobar|)"
		);
	}

	public function testInsertOneValueForMultipleFields() {
		$this->assertTableAndValuesResultInSql(
			'some_table',
			array(
				'some_field' => 'foobar',
				'another_field' => 42,
				'last_field' => 'o_O',
			),
			"INSERT INTO some_table (some_field, another_field, last_field) VALUES (|foobar|, |42|, |o_O|)"
		);
	}

}