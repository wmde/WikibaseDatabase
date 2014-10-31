<?php

namespace Wikibase\Database\Tests\MediaWiki;

use DatabaseBase;
use Wikibase\Database\MediaWiki\DBConnectionProvider;
use Wikibase\Database\MediaWiki\MediaWikiQueryInterface;

/**
 * @covers Wikibase\Database\MediaWiki\MediaWikiQueryInterface
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMediawiki
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiQueryInterfaceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider tableNameProvider
	 *
	 * @param string $tableName
	 */
	public function testTableExists( $tableName ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'tableExists' )
			->with( $this->equalTo( $tableName ) );

		$queryInterface->tableExists( $tableName );
	}

	public function tableNameProvider() {
		$argLists = array();

		$argLists[] = array( 'user' );
		$argLists[] = array( 'xdgxftjhreyetfytj' );
		$argLists[] = array( 'a' );
		$argLists[] = array( 'foo_bar_baz_bah' );

		return $argLists;
	}

	/**
	 * @dataProvider insertProvider
	 */
	public function testInsert( $tableName, array $fieldValues ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( $fieldValues )
			)
			->will( $this->returnValue( true ) );

		$queryInterface->insert(
			$tableName,
			$fieldValues
		);
	}

	/**
	 * @dataProvider insertProvider
	 */
	public function testInsertFailure( $tableName, array $fieldValues ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'insert' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( '\Wikibase\Database\QueryInterface\InsertFailedException' );

		$queryInterface->insert(
			$tableName,
			$fieldValues
		);
	}

	public function insertProvider() {
		$argLists = array();

		$argLists[] = array( 'foo', array() );
		$argLists[] = array( 'bar', array( 'intfield', ) );
		$argLists[] = array( 'baz', array( 'intfield', 'textfield', ) );

		return $argLists;
	}

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdate( $tableName, array $newValues, array $conditions ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'update' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( $newValues ),
				$this->equalTo( $conditions )
			)
			->will( $this->returnValue( true ) );

		$queryInterface->update(
			$tableName,
			$newValues,
			$conditions
		);
	}

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdateFailure( $tableName, array $newValues, array $conditions ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'update' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( '\Wikibase\Database\QueryInterface\UpdateFailedException' );

		$queryInterface->update(
			$tableName,
			$newValues,
			$conditions
		);
	}

	public function updateProvider() {
		$argLists = array();

		$argLists[] = array(
			'foo',
			array( 'intfield', 'textfield', ),
			array(
			)
		);

		$argLists[] = array(
			'foo',
			array( 'textfield', ),
			array( 'intfield' )
		);

		$argLists[] = array(
			'foo',
			array( 'textfield', 'intfield', 'floatfield', ),
			array( 'textfield', 'floatfield', )
		);

		return $argLists;
	}

	/**
	 * @dataProvider deleteProvider
	 */
	public function testDelete( $tableName, array $conditions ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'delete' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( $conditions )
			)
			->will( $this->returnValue( true ) );

		$queryInterface->delete( $tableName, $conditions );
	}

	/**
	 * @dataProvider deleteProvider
	 */
	public function testDeleteFailure( $tableName, array $conditions ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'delete' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( '\Wikibase\Database\Exception\DeleteFailedException' );

		$queryInterface->delete( $tableName, $conditions );
	}

	public function deleteProvider() {
		$argLists = array();

		$argLists[] = array( 'foo', array() );

		$argLists[] = array( 'bar', array(
			'intfield' => 42,
		) );

		$argLists[] = array( 'baz', array(
			'intfield' => 42,
			'textfield' => '~=[,,_,,]:3',
		) );

		return $argLists;
	}

	public function testGetInsertId() {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$connection->expects( $this->once() )
			->method( 'insertId' )
			->will( $this->returnValue( 42 ) );

		$this->assertEquals( 42, $queryInterface->getInsertId() );
	}

	/**
	 * @dataProvider selectProvider
	 */
	public function testSelect( $tableName, array $fields, array $conditions ) {
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection )
		);

		$resultWrapper = $this->getMockBuilder( 'ResultWrapper' )
			->disableOriginalConstructor()->getMock();

		$connection->expects( $this->once() )
			->method( 'select' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( $fields ),
				$this->equalTo( $conditions )
			)
			->will( $this->returnValue( $resultWrapper ) );

		$queryInterface->select( $tableName, $fields, $conditions );

		// Ideally we would have the select method result a mock ResultWrapper
		// and would assert if the data was present in the selection result.
		// It however seems somewhat impossible to create a mock of ResultWrapper.
	}

	public function selectProvider() {
		$argLists = array();

		$argLists[] = array(
			'table',
			array(
				'foo',
				'bar',
				'baz',
			),
			array(
				'intfield' => 42,
				'strfield' => 'nyan',
			)
		);

		$argLists[] = array(
			'table',
			array(
				'foo',
				'bar',
				'baz',
			),
			array(
			)
		);

		$argLists[] = array(
			'onoez',
			array(
				'foo',
			),
			array(
				'intfield' => 42,
			)
		);

		return $argLists;
	}

	public function testSelectFailure() {
		$this->setExpectedException( 'Wikibase\Database\Exception\SelectFailedException' );
		$connection = $this->getMockBuilder( 'DatabaseMysql' )->disableOriginalConstructor()->getMock();
		$connection->expects( $this->once() )
			->method( 'select' )
			->will( $this->returnValue( 'FOOBAR' ) );

		$queryInterface = new MediaWikiQueryInterface( new DirectConnectionProvider( $connection ) );
		$queryInterface->select( 'ham', array( 'egg' ), array( 'chips' ) );
	}

}

class DirectConnectionProvider implements DBConnectionProvider {

	protected $connection;

	public function __construct( DatabaseBase $connection ) {
		$this->connection = $connection;
	}

	/**
	 * @see DBConnectionProvider::getConnection
	 *
	 * @since 0.1
	 *
	 * @return DatabaseBase
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * @see DBConnectionProvider::releaseConnection
	 *
	 * @since 0.1
	 */
	public function releaseConnection() {

	}

}
