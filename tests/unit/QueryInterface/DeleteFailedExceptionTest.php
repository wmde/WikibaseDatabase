<?php

namespace Wikibase\Database\Tests\QueryInterface;

use Wikibase\Database\Exception\DeleteFailedException;

/**
 * @covers Wikibase\Database\Exception\DeleteFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DeleteFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithOnlyRequiredArguments() {
		$tableName = 'nyancats';
		$conditions = array( 'foo' => 42, 'awesome > 9000' );

		$exception = new DeleteFailedException( $tableName, $conditions );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $conditions, $exception->getConditions() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$conditions = array( 'foo' => 42 );
		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new DeleteFailedException( $tableName, $conditions, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $conditions, $exception->getConditions() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
