<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\IndexAdditionFailedException;

/**
 * @covers Wikibase\Database\Schema\IndexAdditionFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IndexAdditionFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithJustATable() {
		$tableName = 'users';
		$index = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\IndexDefinition' )
			->disableOriginalConstructor()->getMock();

		$exception = new IndexAdditionFailedException( $tableName, $index );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $index, $exception->getIndex() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$index = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\IndexDefinition' )
			->disableOriginalConstructor()->getMock();

		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new IndexAdditionFailedException( $tableName, $index, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $index, $exception->getIndex() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
