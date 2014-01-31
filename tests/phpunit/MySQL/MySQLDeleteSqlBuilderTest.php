<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLConditionSqlBuilder;
use Wikibase\Database\MySQL\MySQLDeleteSqlBuilder;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeIdentifierEscaper;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeValueEscaper;

/**
 * @covers Wikibase\Database\MySQL\MySQLDeleteSqlBuilder
 * @covers Wikibase\Database\MySQL\MySQLConditionSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLDeleteSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var MySQLDeleteSqlBuilder
	 */
	protected $sqlBuilder;

	public function setUp() {
		$this->sqlBuilder = new MySQLDeleteSqlBuilder(
			new FakeIdentifierEscaper(),
			new MySQLConditionSqlBuilder(
				new FakeValueEscaper(),
				new FakeIdentifierEscaper()
			)
		);
	}

	public function testGivenNoConditions_noConditionsAreInSql() {
		$this->assertTableAndConditionsResultInSql(
			'some_table',
			array(),
			'DELETE FROM ~some_table~'
		);
	}

	protected function assertTableAndConditionsResultInSql( $tableName, array $conditions, $sql ) {
		$this->assertSame(
			$sql,
			$this->sqlBuilder->getDeleteSql( $tableName, $conditions )
		);
	}

	public function testGivenOneKeyValuePair_returnsOneEqualityCondition() {
		$this->assertTableAndConditionsResultInSql(
			'some_table',
			array(
				'some_field' => 'foobar'
			),
			'DELETE FROM ~some_table~ WHERE ~some_field~=|foobar|'
		);
	}

	public function testGivenTwoKeyValuePairs_returnsTwoEqualityConditions() {
		$this->assertTableAndConditionsResultInSql(
			'some_table',
			array(
				'some_field' => 'foobar',
				'another_field' => 42
			),
			'DELETE FROM ~some_table~ WHERE ~some_field~=|foobar| AND ~another_field~=|42|'
		);
	}

	public function testGivenManyKeyValuePairs_returnsManyEqualityConditions() {
		$this->assertTableAndConditionsResultInSql(
			'some_table',
			array(
				'some_field' => 'foobar',
				'another_field' => 42,
				'third_field' => ''
			),
			'DELETE FROM ~some_table~ WHERE ~some_field~=|foobar| AND ~another_field~=|42| AND ~third_field~=||'
		);
	}

	public function testGivenNoKeys_returnsConditionsAsProvided() {
		$this->assertTableAndConditionsResultInSql(
			'some_table',
			array(
				'some_field=foobar',
				'another_field' => 42,
				'third_field > 9000'
			),
			'DELETE FROM ~some_table~ WHERE some_field=foobar AND ~another_field~=|42| AND third_field > 9000'
		);
	}

	public function testGivenArray_returnsInClause() {
		$this->assertTableAndConditionsResultInSql(
			'some_table',
			array(
				'some_field' => array(
					'foo',
					42,
					'bar'
				)
			),
			'DELETE FROM ~some_table~ WHERE ~some_field~ IN (|foo|, |42|, |bar|)'
		);
	}

}