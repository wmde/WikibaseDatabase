<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\TableCreationFailedException;

/**
 * @covers Wikibase\Database\Schema\TableCreationFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableCreationFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithJustATable() {
		$table = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\TableDefinition' )
			->disableOriginalConstructor()->getMock();

		$exception = new TableCreationFailedException( $table );

		$this->assertEquals( $table, $exception->getTable() );
	}

	public function testConstructorWithAllArguments() {
		$table = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\TableDefinition' )
			->disableOriginalConstructor()->getMock();

		$message = 'NyanData all the way accross the sky!';

		$previous = new \Exception( 'Onoez!' );

		$exception = new TableCreationFailedException( $table, $message, $previous );

		$this->assertEquals( $table, $exception->getTable() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
