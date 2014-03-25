<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\PrefixingTableNameFormatter;

/**
 * @covers Wikibase\Database\PrefixingTableNameFormatter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PrefixingTableNameFormatterTest extends \PHPUnit_Framework_TestCase {

	public function testGivenNoPrefix_doesNotChangeName() {
		$formatter = new PrefixingTableNameFormatter( '' );

		$this->assertEquals(
			'foobar-baz',
			$formatter->formatTableName( 'foobar-baz' )
		);
	}

	public function testGivenPrefix_nameGetsPrefixed() {
		$formatter = new PrefixingTableNameFormatter( 'prefix_' );

		$this->assertEquals(
			'prefix_foobar-baz',
			$formatter->formatTableName( 'foobar-baz' )
		);
	}

}