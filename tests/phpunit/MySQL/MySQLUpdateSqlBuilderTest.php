<?php

namespace Wikibase\Database\Tests\MySQL;

use Wikibase\Database\MySQL\MySQLUpdateSqlBuilder;

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

	public function testTodo() {
		$this->assertTrue( (bool)'TODO' );
	}

}