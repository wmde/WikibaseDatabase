<?php

namespace Wikibase\Database\Tests\MediaWiki;

use DatabaseBase;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\MediaWiki\MediaWikiQueryInterface;
use Wikibase\Database\TableDefinition;

/**
 * @covers Wikibase\Database\MediaWiki\MediaWikiQueryInterface
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseDatabaseTest
 *
 * @group Wikibase
 * @group WikibaseDatabase
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
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
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
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testCreateTable( TableDefinition $table ) {
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$tableSqlBuilder->expects( $this->once() )
			->method( 'getCreateTableSql' )
			->with( $this->equalTo( $table ) )
			->will( $this->returnValue( 'foo bar baz' ) );

		$connection->expects( $this->once() )
			->method( 'query' )
			->with( $this->equalTo( 'foo bar baz' ) )
			->will( $this->returnValue( true ) );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
		);

		$queryInterface->createTable( $table );
	}

	/**
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testCreateTableFailure( TableDefinition $table ) {
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
		);

		$connection->expects( $this->once() )
			->method( 'query' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( 'Wikibase\Database\TableCreationFailedException' );

		$queryInterface->createTable( $table );
	}

	/**
	 * @dataProvider tableProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testDropTable( TableDefinition $table ) {
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
		);

		$connection->expects( $this->once() )
			->method( 'dropTable' )
			->with( $this->equalTo( $table ) );

		$queryInterface->dropTable( $table );
	}

	public function tableProvider() {
		$tables = array();

		$tables[] = new TableDefinition( 'differentfieldtypes', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER ),
			new FieldDefinition( 'floatfield', FieldDefinition::TYPE_FLOAT ),
			new FieldDefinition( 'textfield', FieldDefinition::TYPE_TEXT ),
			new FieldDefinition( 'boolfield', FieldDefinition::TYPE_BOOLEAN ),
		) );

		$tables[] = new TableDefinition( 'defaultfieldvalues', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER, true, 42 ),
		) );

		$tables[] = new TableDefinition( 'notnullfields', array(
			new FieldDefinition( 'intfield', FieldDefinition::TYPE_INTEGER, false ),
			new FieldDefinition( 'textfield', FieldDefinition::TYPE_TEXT, false ),
		) );

		$argLists = array();

		foreach ( $tables as $table ) {
			$argLists[] = array( $table );
		}

		return $argLists;
	}

	/**
	 * @dataProvider insertProvider
	 */
	public function testInsert( $tableName, array $fieldValues ) {
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMockBuilder( 'Wikibase\Database\TableSqlBuilder' )
			->disableOriginalConstructor()->getMock();

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
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
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
		);

		$connection->expects( $this->once() )
			->method( 'insert' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( '\Wikibase\Database\InsertFailedException' );

		$queryInterface->insert(
			$tableName,
			$fieldValues
		);
	}

	public function insertProvider() {
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

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdate( $tableName, array $newValues, array $conditions ) {
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
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
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
		);

		$connection->expects( $this->once() )
			->method( 'update' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( '\Wikibase\Database\UpdateFailedException' );

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
			array(
				'intfield' => 42,
				'textfield' => 'foobar baz',
			),
			array(
			)
		);

		$argLists[] = array(
			'foo',
			array(
				'textfield' => '~=[,,_,,]:3',
			),
			array(
				'intfield' => 0
			)
		);

		$argLists[] = array(
			'foo',
			array(
				'textfield' => '~=[,,_,,]:3',
				'intfield' => 0,
				'floatfield' => 4.2,
			),
			array(
				'textfield' => '~[,,_,,]:3',
				'floatfield' => 9000.1,
			)
		);

		return $argLists;
	}

	/**
	 * @dataProvider deleteProvider
	 */
	public function testDelete( $tableName, array $conditions ) {
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
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
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
		);

		$connection->expects( $this->once() )
			->method( 'delete' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( '\Wikibase\Database\DeleteFailedException' );

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
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
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
		$connection = $this->getMock( 'DatabaseMysql' );
		$tableSqlBuilder = $this->getMock( 'Wikibase\Database\TableSqlBuilder' );

		$queryInterface = new MediaWikiQueryInterface(
			new DirectConnectionProvider( $connection ),
			$tableSqlBuilder
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
