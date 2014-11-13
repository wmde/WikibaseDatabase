<?php

namespace Wikibase\Database\Tests\QueryInterface;

use Wikibase\Database\Exception\SelectFailedException;

/**
 * @covers Wikibase\Database\Exception\SelectFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SelectFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithOnlyRequiredArguments() {
		$tableName = 'nyancats';
		$fields = array( 'bar', 'baz', 'bah' );
		$conditions = array( 'foo' => 42, 'awesome > 9000' );

		$exception = new SelectFailedException( $tableName, $fields, $conditions );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $fields, $exception->getFields() );
		$this->assertEquals( $conditions, $exception->getConditions() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$fields = array( 'bar' );
		$conditions = array( 'foo' => 42 );
		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new SelectFailedException( $tableName, $fields, $conditions, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $fields, $exception->getFields() );
		$this->assertEquals( $conditions, $exception->getConditions() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
