<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\TableDeletionFailedException;

/**
 * @covers Wikibase\Database\Schema\TableDeletionFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableDeletionFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithJustATable() {
		$tableName = 'foo';

		$exception = new TableDeletionFailedException( $tableName );

		$this->assertEquals( $tableName, $exception->getTableName() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'foo';
		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new TableDeletionFailedException( $tableName, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
