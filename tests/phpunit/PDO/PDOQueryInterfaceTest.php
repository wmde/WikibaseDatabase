<?php

namespace Wikibase\Database\Tests\PDO;

use Wikibase\Database\PDO\PDOQueryInterface;

/**
 * @covers Wikibase\Database\PDO\PDOQueryInterface
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOQueryInterfaceTest extends \PHPUnit_Framework_TestCase {

	protected $tableName = 'someTable';
	protected $values = array(
		'leet' => 3117,
		'awesome' => '~=[,,_,,]:3'
	);
	protected $conditions = array( 'bar' => 'baz' );
	protected $fields = array( 'foo', 'bar', 'bah' );
	protected $stubSql = 'STEAL ALL OF THE FOOD';

	public function testInsertCallsSqlBuilderAndPdo() {
		$db = $this->newQueryInterfaceForInsert( true );

		$db->insert( $this->tableName, $this->values );
	}

	protected function newQueryInterfaceForInsert( $insertCallReturnValue ) {
		$insertBuilder = $this->getMock( 'Wikibase\Database\QueryInterface\InsertSqlBuilder' );

		$insertBuilder->expects( $this->once() )
			->method( 'getInsertSql' )
			->with(
				$this->equalTo( $this->tableName ),
				$this->equalTo( $this->values )
			)
			->will( $this->returnValue( $this->stubSql ) );

		return $this->newQueryInterface( $insertCallReturnValue, 'InsertSqlBuilder', $insertBuilder );
	}

	public function testOnReceiveOfFalse_insertThrowsInsertError() {
		$db = $this->newQueryInterfaceForInsert( false );

		$this->setExpectedException( 'Wikibase\Database\QueryInterface\InsertFailedException' );
		$db->insert( $this->tableName, $this->values );
	}

	public function testUpdateCallsSqlBuilderAndPdo() {
		$db = $this->newQueryInterfaceForUpdate( true );
		$db->update( $this->tableName, $this->values, $this->conditions );
	}

	public function testOnReceiveOfFalse_updateThrowsUpdateError() {
		$db = $this->newQueryInterfaceForUpdate( false );

		$this->setExpectedException( 'Wikibase\Database\QueryInterface\UpdateFailedException' );
		$db->update( $this->tableName, $this->values, $this->conditions );
	}

	protected function newPdoMock( $returnValue ) {
		$pdo = $this->getMock( 'Wikibase\Database\Tests\PDO\PDOMock' );

		$pdo->expects( $this->once() )
			->method( 'query' )
			->with( $this->equalTo( $this->stubSql ) )
			->will( $this->returnValue( $returnValue ) );

		return $pdo;
	}

	protected function newQueryInterfaceForUpdate( $updateCallReturnValue ) {
		$updateBuilder = $this->getMock( 'Wikibase\Database\QueryInterface\UpdateSqlBuilder' );

		$updateBuilder->expects( $this->once() )
			->method( 'getUpdateSql' )
			->with(
				$this->equalTo( $this->tableName ),
				$this->equalTo( $this->values ),
				$this->equalTo( $this->conditions )
			)
			->will( $this->returnValue( $this->stubSql ) );

		return $this->newQueryInterface( $updateCallReturnValue, 'UpdateSqlBuilder', $updateBuilder );
	}

	protected function newQueryInterface( $pdoReturnValue, $collaboratorName, $collaboratorInstance ) {
		$pdo = $this->newPdoMock( $pdoReturnValue );

		$sqlBuilders = array(
			'InsertSqlBuilder' => $this->getMock( 'Wikibase\Database\QueryInterface\InsertSqlBuilder' ),
			'UpdateSqlBuilder' => $this->getMock( 'Wikibase\Database\QueryInterface\UpdateSqlBuilder' ),
			'DeleteSqlBuilder' => $this->getMock( 'Wikibase\Database\QueryInterface\DeleteSqlBuilder' ),
			'SelectSqlBuilder' => $this->getMock( 'Wikibase\Database\QueryInterface\SelectSqlBuilder' ),
			'InsertedIdSqlBuilder' => $this->getMock( 'Wikibase\Database\QueryInterface\InsertedIdSqlBuilder' ),
		);

		$sqlBuilders[$collaboratorName] = $collaboratorInstance;

		$insertBuilder = $sqlBuilders['InsertSqlBuilder'];
		$updateBuilder = $sqlBuilders['UpdateSqlBuilder'];
		$deleteBuilder = $sqlBuilders['DeleteSqlBuilder'];
		$selectBuilder = $sqlBuilders['SelectSqlBuilder'];
		$idBuilder = $sqlBuilders['InsertedIdSqlBuilder'];

		return new PDOQueryInterface(
			$pdo,
			$insertBuilder,
			$updateBuilder,
			$deleteBuilder,
			$selectBuilder,
			$idBuilder
		);
	}

	public function testDeleteCallsSqlBuilderAndPdo() {
		$db = $this->newQueryInterfaceForDelete( true );
		$db->delete( $this->tableName, $this->conditions );
	}

	public function testOnReceiveOfFalse_deleteThrowsDeleteError() {
		$db = $this->newQueryInterfaceForDelete( false );

		$this->setExpectedException( 'Wikibase\Database\QueryInterface\DeleteFailedException' );
		$db->delete( $this->tableName, $this->conditions );
	}

	protected function newQueryInterfaceForDelete( $deleteCallReturnValue ) {
		$deleteBuilder = $this->getMock( 'Wikibase\Database\QueryInterface\DeleteSqlBuilder' );

		$deleteBuilder->expects( $this->once() )
			->method( 'getDeleteSql' )
			->with(
				$this->equalTo( $this->tableName ),
				$this->equalTo( $this->conditions )
			)
			->will( $this->returnValue( $this->stubSql ) );

		return $this->newQueryInterface( $deleteCallReturnValue, 'DeleteSqlBuilder', $deleteBuilder );
	}

	protected function newQueryInterfaceForSelect( $selectCallReturnValue ) {
		$selectBuilder = $this->getMock( 'Wikibase\Database\QueryInterface\SelectSqlBuilder' );

		$selectBuilder->expects( $this->once() )
			->method( 'getSelectSql' )
			->with(
				$this->equalTo( $this->tableName ),
				$this->equalTo( $this->fields ),
				$this->equalTo( $this->conditions )
			)
			->will( $this->returnValue( $this->stubSql ) );

		return $this->newQueryInterface( $selectCallReturnValue, 'SelectSqlBuilder', $selectBuilder );
	}

	public function testSelectCallsSqlBuilderAndPdo() {
		$db = $this->newQueryInterfaceForSelect( $this->getMock( 'PDOStatement' ) );
		$db->select( $this->tableName, $this->fields, $this->conditions );
	}

	public function testOnReceiveOfFalse_selectThrowsSelectError() {
		$db = $this->newQueryInterfaceForSelect( false );

		$this->setExpectedException( 'Wikibase\Database\QueryInterface\SelectFailedException' );
		$db->select( $this->tableName, $this->fields, $this->conditions );
	}

	public function testOnReceivePDOStatement_selectReturnsIterator() {
		$pdoStatement = $this->getMock( 'PDOStatement' );

		$db = $this->newQueryInterfaceForSelect( $pdoStatement );
		$result = $db->select( $this->tableName, $this->fields, $this->conditions );

		$this->assertInstanceOf( 'Iterator', $result );

		// TODO: assert contents
	}

	protected function newQueryInterfaceForGetInsertId( $getIdCallReturnValue ) {
		$insertedIdBuilder = $this->getMock( 'Wikibase\Database\QueryInterface\InsertedIdSqlBuilder' );

		$insertedIdBuilder->expects( $this->once() )
			->method( 'getSqlToGetTheInsertedId' )
			->will( $this->returnValue( $this->stubSql ) );

		return $this->newQueryInterface( $getIdCallReturnValue, 'InsertedIdSqlBuilder', $insertedIdBuilder );
	}

	public function testGetInsertIdCallsCollaboratorsAndReturnsInt() {
		$db = $this->newQueryInterfaceForGetInsertId( '1337' );
		$id = $db->getInsertId();

		$this->assertInternalType( 'int', $id );
		$this->assertEquals( 1337, $id );
	}

	public function testOnReceiveOfFalse_getInsertIdThrows() {
		$db = $this->newQueryInterfaceForGetInsertId( false );

		$this->setExpectedException( 'Wikibase\Database\QueryInterface\QueryInterfaceException' );
		$db->getInsertId();
	}


}
