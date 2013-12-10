<?php

namespace Wikibase\Database\Tests\Standalone;

use Wikibase\Database\Standalone\StandaloneQueryInterface;

/**
 * @covers Wikibase\Database\Standalone\StandaloneQueryInterface
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StandaloneQueryInterfaceTest extends \PHPUnit_Framework_TestCase {

	public function testInsertCallsSqlBuilderAndPdo() {
		$tableName = 'someTable';
		$values = array(
			'leet' => 3117,
			'awesome' => '~=[,,_,,]:3'
		);

		$pdo = $this->newPdoMock();

		$pdo->expects( $this->once() )
			->method( 'query' );

		$insertBuilder = $this->getMock( 'Wikibase\Database\QueryInterface\InsertSqlBuilder' );

		$insertBuilder->expects( $this->once() )
			->method( 'getInsertSql' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( $values )
			)
			->will( $this->returnValue( true ) );

		$db = new StandaloneQueryInterface( $pdo, $insertBuilder );

		$db->insert( $tableName, $values );
	}

	protected function newPdoMock() {
		return $this->getMock( 'Wikibase\Database\Tests\Standalone\PDOMock' );
	}

}
