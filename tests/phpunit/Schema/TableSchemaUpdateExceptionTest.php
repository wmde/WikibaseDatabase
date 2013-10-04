<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\TableSchemaUpdateException;

/**
 * @covers Wikibase\Database\Schema\TableSchemaUpdateException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableSchemaUpdateExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithOnlyRequiredArguments() {
		new TableSchemaUpdateException();
		$this->assertTrue( true );
	}

	public function testConstructorWithAllArguments() {
		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new TableSchemaUpdateException( $message, $previous );

		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
