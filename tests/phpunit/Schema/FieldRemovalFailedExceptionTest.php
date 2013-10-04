<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\FieldRemovalFailedException;

/**
 * @covers Wikibase\Database\Schema\FieldRemovalFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FieldRemovalFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithJustATable() {
		$tableName = 'users';
		$fieldName = 'btc';

		$exception = new FieldRemovalFailedException( $tableName, $fieldName );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $fieldName, $exception->getFieldName() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$fieldName = 'btc';

		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new FieldRemovalFailedException( $tableName, $fieldName, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $fieldName, $exception->getFieldName() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
