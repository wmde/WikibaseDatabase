<?php

namespace Wikibase\Database\Tests\PDO;

use Wikibase\Database\PDO\PDOEscaper;

/**
 * @covers Wikibase\Database\PDO\PDOEscaper
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOEscaperTest extends \PHPUnit_Framework_TestCase {

	public function testCallsPdoQuote() {
		$input = 'foo';

		$pdo = $this->newPdoMock();

		$pdo->expects( $this->once() )
			->method( 'quote' )
			->with( $input )
			->will( $this->returnCallback( array( $this, 'dummyQuoteValue' ) ) );

		$escaper = new PDOEscaper( $pdo );
		$actual = $escaper->getEscapedValue( $input );

		$this->assertEquals( $this->dummyQuoteValue( $input ), $actual );
	}

	public function dummyQuoteValue( $input ) {
		return '|' . $input . '|';
	}

	private function newPdoMock() {
		return $this->getMock( 'Wikibase\Database\Tests\PDO\PDOStub' );
	}

	/**
	 * @dataProvider identifierProvider
	 */
	public function testIdentifiersGetEscaped( $identifier, $escapedIdentifier ) {
		$escaper = new PDOEscaper( $this->newPdoMock() );

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
