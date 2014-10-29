<?php

namespace Wikibase\Database\Tests\QueryInterface;

use Wikibase\Database\Exception\UpdateFailedException;

/**
 * @covers Wikibase\Database\Exception\UpdateFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UpdateFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithOnlyRequiredArguments() {
		$tableName = 'nyancats';
		$values = array( 'bar', 'baz', 'bah' );
		$conditions = array( 'foo' => 42, 'awesome > 9000' );

		$exception = new UpdateFailedException( $tableName, $values, $conditions );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $values, $exception->getValues() );
		$this->assertEquals( $conditions, $exception->getConditions() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$fields = array( 'bar' );
		$conditions = array( 'foo' => 42 );
		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new UpdateFailedException( $tableName, $fields, $conditions, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $fields, $exception->getValues() );
		$this->assertEquals( $conditions, $exception->getConditions() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
