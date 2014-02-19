<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLConditionSqlBuilder;
use Wikibase\Database\MySQL\MySQLSelectSqlBuilder;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeIdentifierEscaper;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeValueEscaper;

/**
 * @covers Wikibase\Database\MySQL\MySQLSelectSqlBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMySQL
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLSelectSqlBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var MySQLSelectSqlBuilder
	 */
	private $selectBuilder;

	public function setUp() {
		$this->selectBuilder = new MySQLSelectSqlBuilder(
			new FakeIdentifierEscaper(),
			new MySQLConditionSqlBuilder(
				new FakeValueEscaper(),
				new FakeIdentifierEscaper()
			)
		);
	}

	public function testSelectOneFieldWithoutConditions() {
		$sql = $this->selectBuilder->getSelectSql(
			'some_table',
			array(
				'some_field',
			),
			array()
		);

		$this->assertEquals(
			'SELECT ~some_field~ FROM ~some_table~',
			$sql
		);
	}

	public function testSelectMultipleFieldsWithoutConditions() {
		$sql = $this->selectBuilder->getSelectSql(
			'some_table',
			array(
				'some_field',
				'another_field',
				'ThirdFieldName',
			),
			array()
		);

		$this->assertEquals(
			'SELECT ~some_field~, ~another_field~, ~ThirdFieldName~ FROM ~some_table~',
			$sql
		);
	}

	public function testSelectMultipleFieldsFromMultipleTablesWithoutConditions() {
		$sql = $this->selectBuilder->getSelectSql(
			array( 'table1', 'table2' ),
			array(
				'table1.a',
				'table2.b',
			),
			array()
		);

		$this->assertEquals(
			'SELECT ~table1.a~, ~table2.b~ FROM ~table1~, ~table2~',
			$sql
		);
	}

	public function testSelectWithSomeConditions() {
		$sql = $this->selectBuilder->getSelectSql(
			'some_table',
			array(
				'some_field',
				'another_field',
			),
			array(
				'some_field > 9000',
				'another_field' => 42
			)
		);

		$this->assertEquals(
			'SELECT ~some_field~, ~another_field~ FROM ~some_table~ WHERE some_field > 9000 AND ~another_field~=|42|',
			$sql
		);
	}

}