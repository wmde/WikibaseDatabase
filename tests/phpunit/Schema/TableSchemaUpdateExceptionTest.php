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
		$currentTable = $this->newMockTable();
		$newTable = $this->newMockTable();

		new TableSchemaUpdateException( $currentTable, $newTable );
		$this->assertTrue( true );
	}

	protected function newMockTable() {
		return $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\TableDefinition' )
			->disableOriginalConstructor()->getMock();
	}

	public function testConstructorWithAllArguments() {
		$currentTable = $this->newMockTable();
		$newTable = $this->newMockTable();

		$message = 'NyanData all the way accross the sky!';
		$previous = new \Exception( 'Onoez!' );

		$exception = new TableSchemaUpdateException( $currentTable, $newTable, $message, $previous );

		$this->assertEquals( $currentTable, $exception->getCurrentTable() );
		$this->assertEquals( $newTable, $exception->getNewTable() );
		$this->assertEquals( $message, $exception->getMessage() );
		$this->assertEquals( $previous, $exception->getPrevious() );
	}

}
