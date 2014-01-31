<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLSelectSqlBuilder;
use Wikibase\Database\Tests\TestDoubles\Fakes\FakeIdentifierEscaper;

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
			new FakeIdentifierEscaper()
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

}