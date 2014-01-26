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
		$this->sqlBuilder = new MySQLInsertSqlBuilder();
	}

	public function testGivenNoValues_returnsEmptyString() {
		$this->assertSame(
			'',
			$this->sqlBuilder->getInsertSql( 'some_table', array() )
		);
	}

}