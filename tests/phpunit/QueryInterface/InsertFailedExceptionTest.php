<?php

namespace Wikibase\Database\Tests\QueryInterface;

use Wikibase\Database\Exception\InsertFailedException;

/**
 * @covers Wikibase\Database\Exception\InsertFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InsertFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithOnlyRequiredArguments() {
		$tableName = 'nyancats';
		$values = array( 'foo' => 42, 'awesome > 9000' );

		$exception = new InsertFailedException( $tableName, $values );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $values, $exception->getValues() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$values = array( 'foo' => 42 );
		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new InsertFailedException( $tableName, $values, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $values, $exception->getValues() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
