<?php

namespace Wikibase\Database\Tests\Schema;

use Wikibase\Database\Schema\IndexRemovalFailedException;

/**
 * @covers Wikibase\Database\Schema\IndexRemovalFailedException
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IndexRemovalFailedExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithJustATable() {
		$tableName = 'users';
		$indexName = 'btc';

		$exception = new IndexRemovalFailedException( $tableName, $indexName );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $indexName, $exception->getIndexName() );
	}

	public function testConstructorWithAllArguments() {
		$tableName = 'users';
		$indexName = 'btc';

		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new IndexRemovalFailedException( $tableName, $indexName, $message, $previous );

		$this->assertEquals( $tableName, $exception->getTableName() );
		$this->assertEquals( $indexName, $exception->getIndexName() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
