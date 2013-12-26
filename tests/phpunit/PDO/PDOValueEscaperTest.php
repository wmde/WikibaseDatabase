<?php

namespace Wikibase\Database\Tests\PDO;

use Wikibase\Database\PDO\PDOValueEscaper;

/**
 * @covers Wikibase\Database\PDO\PDOValueEscaper
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOValueEscaperTest extends \PHPUnit_Framework_TestCase {

	public function testCallsPdoQuote() {
		$input = 'foo';

		$pdo = $this->newPdoMock();

		$pdo->expects( $this->once() )
			->method( 'quote' )
			->with( $input )
			->will( $this->returnCallback( array( $this, 'dummyQuoteValue' ) ) );

		$escaper = new PDOValueEscaper( $pdo );
		$actual = $escaper->getEscapedValue( $input );

		$this->assertEquals( $this->dummyQuoteValue( $input ), $actual );
	}

	public function inputProvider() {

	}

	public function dummyQuoteValue( $input ) {
		return '|' . $input . '|';
	}

	protected function newPdoMock() {
		return $this->getMock( 'Wikibase\Database\Tests\PDO\PDOMock' );
	}

}
