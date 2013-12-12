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

	protected $stubSql = 'STEAL ALL OF THE FOOD';
	protected $tableName = 'someTable';
	protected $values = array(
		'leet' => 3117,
		'awesome' => '~=[,,_,,]:3'
	);
	protected $conditions = array( 'bar' => 'baz' );

	public function testInsertCallsSqlBuilderAndPdo() {
		$db = $this->newQueryInterfaceForInsert( true );

		$db->insert( $this->tableName, $this->values );
	}

	protected function newQueryInterfaceForInsert( $insertCallReturnValue ) {
		$pdo = $this->newPdoMock();

		$pdo->expects( $this->once() )
			->method( 'query' )
			->with( $this->equalTo( $this->stubSql ) )
			->will( $this->returnValue( $insertCallReturnValue ) );

		$insertBuilder = $this->getMock( 'Wikibase\Database\QueryInterface\InsertSqlBuilder' );

		$insertBuilder->expects( $this->once() )
			->method( 'getInsertSql' )
			->with(
				$this->equalTo( $this->tableName ),
				$this->equalTo( $this->values )
			)
			->will( $this->returnValue( $this->stubSql ) );

		return new StandaloneQueryInterface( $pdo, $insertBuilder );
	}

	protected function newPdoMock() {
		return $this->getMock( 'Wikibase\Database\Tests\Standalone\PDOMock' );
	}

	public function testOnReceiveOfFalse_insertThrowsInsertError() {
		$db = $this->newQueryInterfaceForInsert( false );

		$this->setExpectedException( 'Wikibase\Database\QueryInterface\InsertFailedException' );
		$db->insert( $this->tableName, $this->values );
	}

//	public function testOnReceiveOfFalse_updateThrowsUpdateError() {
//		$db = $this->newQueryInterfaceForUpdate( false );
//
//		$this->setExpectedException( 'Wikibase\Database\QueryInterface\UpdateFailedException' );
//		$db->update( $this->tableName, $this->values. $this->conditions );
//	}

}
