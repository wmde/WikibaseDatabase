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

	public function testNewMySQLQueryInterface_returnsQueryInterface() {
		$factory = new PDOFactory( $this->getMock( 'Wikibase\Database\Tests\PDO\PDOStub' ) );

		$queryInterface = $factory->newMySQLQueryInterface();

		$this->assertInstanceOf( 'Wikibase\Database\QueryInterface\QueryInterface', $queryInterface );
	}

}
