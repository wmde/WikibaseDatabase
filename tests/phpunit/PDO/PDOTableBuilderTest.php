<?php

namespace Wikibase\Database\Tests\PDO;

use Wikibase\Database\PDO\PDOQueryInterface;
use Wikibase\Database\PDO\PDOTableBuilder;

/**
 * @covers Wikibase\Database\PDO\PDOTableBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOTableBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCreateTableCallsCollaborators() {
		$stubSql = 'foo bar baz';

		$pdo = $this->newMockPdo( $stubSql, true );

		$table = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\TableDefinition' )
			->disableOriginalConstructor()->getMock();

		$sqlBuilder = $this->newMockSqlBuilder( $table, $stubSql );

		$tableBuilder = new PDOTableBuilder( $pdo, $sqlBuilder );
		$tableBuilder->createTable( $table );
	}

	private function newMockPdo( $stubSql, $queryReturnValue ) {
		$pdo = $this->getMock( 'Wikibase\Database\Tests\PDO\PDOMock' );

		$pdo->expects( $this->once() )
			->method( 'query' )
			->with( $this->equalTo( $stubSql ) )
			->will( $this->returnValue( $queryReturnValue ) );

		return $pdo;
	}

	private function newMockSqlBuilder( $table, $stubSql ) {
		$sqlBuilder = $this->getMock( 'Wikibase\Database\Schema\TableSqlBuilder' );

		$sqlBuilder->expects( $this->once() )
			->method( 'getCreateTableSql' )
			->with( $table )
			->will( $this->returnValue( $stubSql ) );

		return $sqlBuilder;
	}

	public function testCreateTableThrowsExceptionWhenQueryFails() {
		$stubSql = 'foo bar baz';

		$pdo = $this->newMockPdo( $stubSql, false );

		$table = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\TableDefinition' )
			->disableOriginalConstructor()->getMock();

		$sqlBuilder = $this->newMockSqlBuilder( $table, $stubSql );

		$tableBuilder = new PDOTableBuilder( $pdo, $sqlBuilder );

		$this->setExpectedException( 'Wikibase\Database\Schema\TableCreationFailedException' );
		$tableBuilder->createTable( $table );
	}

}
