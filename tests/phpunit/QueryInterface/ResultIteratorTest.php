<?php

namespace Wikibase\Database\Tests\QueryInterface;

use Wikibase\Database\QueryInterface\ResultIterator;

/**
 * @covers Wikibase\Database\QueryInterface\TableBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ResultIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new ResultIterator( array() );
		$this->assertTrue( true );
	}

	/**
	 * @dataProvider rowProvider
	 */
	public function testRetainsInputData( array $inputRows ) {
		$iterator = new ResultIterator( $inputRows );

		$this->assertEquals(
			$inputRows,
			iterator_to_array( $iterator )
		);
	}

	public function rowProvider() {
		$argLists = array();

		$argLists[] = array( array(
		) );

		$argLists[] = array( array(
			(object)array( 'foo' => 4, 'bar' => 2 ),
		) );

		$argLists[] = array( array(
			(object)array( 'foo' => 4, 'bar' => 2 ),
			(object)array( 'foo' => 1, 'bar' => 3 ),
		) );

		$argLists[] = array( array(
			(object)array( 'foo' => 4, 'bar' => 2 ),
			(object)array( 'foo' => 1, 'bar' => 3 ),
			(object)array( 'baz' => 'nyan', 'bah' => 'cat' ),
		) );

		return $argLists;
	}

}
