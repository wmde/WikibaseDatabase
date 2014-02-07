<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLConditionSqlBuilder;
use Wikibase\Database\MySQL\MySQLUpdateSqlBuilder;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeEscaper;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeIdentifierEscaper;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeValueEscaper;

/**
 * @covers Wikibase\Database\MySQL\MySQLUpdateSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLUpdateSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var MySQLUpdateSqlBuilder
	 */
	private $selectBuilder;

	public function setUp() {
		$this->selectBuilder = new MySQLUpdateSqlBuilder(
			new FakeEscaper(),
			new FakeTableNameFormatter(),
			new MySQLConditionSqlBuilder(
				new FakeValueEscaper(),
				new FakeIdentifierEscaper()
			)
		);
	}

	public function testUpdateOneFieldWithoutConditions() {
		$sql = $this->selectBuilder->getUpdateSql(
			'some_table',
			array(
				'some_field' => 'foo',
			),
			array()
		);

		$this->assertEquals(
			'UPDATE ~prefix_some_table~ SET ~some_field~=|foo|',
			$sql
		);
	}

	public function testUpdateMultipleFieldWithoutConditions() {
		$sql = $this->selectBuilder->getUpdateSql(
			'some_table',
			array(
				'some_field' => 'foo',
				'another_field' => 42,
				'ThirdField' => '',
			),
			array()
		);

		$this->assertEquals(
			'UPDATE ~prefix_some_table~ SET ~some_field~=|foo|, ~another_field~=|42|, ~ThirdField~=||',
			$sql
		);
	}

	public function testGivenNoFields_returnsEmptyString() {
		$sql = $this->selectBuilder->getUpdateSql(
			'some_table',
			array(
			),
			array()
		);

		$this->assertSame(
			'',
			$sql
		);
	}

	public function testGivenNoFieldsThoughConditions_returnsEmptyString() {
		$sql = $this->selectBuilder->getUpdateSql(
			'some_table',
			array(
			),
			array(
				'foo' => 'bar',
				'foobar>9000',
			)
		);

		$this->assertSame(
			'',
			$sql
		);
	}

	public function testUpdateGivenSomeConditions() {
		$sql = $this->selectBuilder->getUpdateSql(
			'some_table',
			array(
				'some_field' => 'foo',
			),
			array(
				'another_field' => 42,
				'foo>bar'
			)
		);

		$this->assertEquals(
			'UPDATE ~prefix_some_table~ SET ~some_field~=|foo| WHERE ~another_field~=|42| AND foo>bar',
			$sql
		);
	}

}