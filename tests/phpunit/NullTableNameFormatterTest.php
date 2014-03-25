<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\NullTableNameFormatter;

/**
 * @covers Wikibase\Database\NullTableNameFormatter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NullTableNameFormatterTest extends \PHPUnit_Framework_TestCase {

	public function testDoesNotChangeName() {
		$formatter = new NullTableNameFormatter();

		$this->assertEquals(
			'foobar-baz',
			$formatter->formatTableName( 'foobar-baz' )
		);
	}

}