<?php

namespace Wikibase\Database\Tests\Standalone;

use Wikibase\Database\Standalone\StandaloneQueryInterface;

/**
 * @covers Wikibase\Database\Standalone\StandaloneQueryInterface
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StandaloneQueryInterfaceTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$db = new StandaloneQueryInterface();
		$this->assertTrue( true );
	}

}
