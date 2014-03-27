<?php

namespace Wikibase\Database\Tests\PDO;

use Wikibase\Database\PDO\PDOFactory;

/**
 * @covers Wikibase\Database\PDO\PDOFactory
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOFactoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var PDOFactory
	 */
	private $factory;

	public function setUp() {
		$this->factory = new PDOFactory( $this->getMock( 'Wikibase\Database\Tests\PDO\PDOStub' ) );
	}

	public function testNewMySQLQueryInterface_returnsQueryInterface() {
		$this->assertInstanceOf(
			'Wikibase\Database\QueryInterface\QueryInterface',
			$this->factory->newMySQLQueryInterface()
		);
	}

	public function testNewMySQLTableBuilder_returnsTableBuilder() {
		$this->assertInstanceOf(
			'Wikibase\Database\Schema\TableBuilder',
			$this->factory->newMySQLTableBuilder( 'foo' )
		);
	}

	public function testNewSQLiteTableBuilder_returnsTableBuilder() {
		$this->assertInstanceOf(
			'Wikibase\Database\Schema\TableBuilder',
			$this->factory->newSQLiteTableBuilder()
		);
	}

}
