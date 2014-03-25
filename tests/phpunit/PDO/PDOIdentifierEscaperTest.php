<?php

namespace Wikibase\Database\Tests\PDO;

use Wikibase\Database\PDO\PDOIdentifierEscaper;

/**
 * @covers Wikibase\Database\PDO\PDOIdentifierEscaper
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOEIdentifierscaperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider identifierProvider
	 */
	public function testIdentifiersGetEscaped( $identifier, $escapedIdentifier ) {
		$escaper = new PDOIdentifierEscaper( $this->getMock( 'Wikibase\Database\Tests\PDO\PDOStub' ) );

		$this->assertEquals(
			$escapedIdentifier,
			$escaper->getEscapedIdentifier( $identifier )
		);
	}

	public function identifierProvider() {
		return array(
			array(
				'',
				'``'
			),

			array(
				'foo',
				'`foo`'
			),

			array(
				'foo bar baz',
				'`foo bar baz`'
			),

			array(
				'foo`bar',
				'`foo``bar`'
			),

			array(
				'`foo`bar`',
				'```foo``bar```'
			),
		);
	}

}
