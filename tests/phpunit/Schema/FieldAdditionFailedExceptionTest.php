<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\FieldAdditionFailedException;

/**
 * @covers Wikibase\Database\Schema\FieldAdditionFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FieldAdditionFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithJustATable() {
		$tableName = 'users';
		$field = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\FieldDefinition' )
			->disableOriginalConstructor()->getMock();

		$exception = new FieldAdditionFailedException( $tableName, $field );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $field, $exception->getField() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$field = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\FieldDefinition' )
			->disableOriginalConstructor()->getMock();

		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new FieldAdditionFailedException( $tableName, $field, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $field, $exception->getField() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
