<?php

namespace Wikibase\Database\Tests;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TestClassesLoadingTest extends \PHPUnit_Framework_TestCase {

	public function testCanLoadFakes() {
		$this->assertTrue( class_exists( 'Wikibase\Database\Tests\TestDoubles\Fakes\FakeTableNameFormatter' ) );
	}

}