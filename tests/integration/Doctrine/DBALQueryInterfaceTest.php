<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Wikibase\Database\Doctrine\DBALQueryInterface;
use Wikibase\Database\QueryInterface;

/**
 * @covers Wikibase\Database\Doctrine\DBALQueryInterface
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseDoctrine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DBALQueryInterfaceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var QueryInterface
	 */
	private $queryInterface;

	const TABLE_NAME = 'kittens';

	public function setUp() {
		$connection = DriverManager::getConnection( array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		) );

		$connection->getSchemaManager()->createTable( $this->newTable() );

		$this->queryInterface = new DBALQueryInterface( $connection );
	}

	private function newTable() {
		$table = new Table( self::TABLE_NAME );

		$table->addColumn(
			'row_id',
			Type::INTEGER,
			array(
				'autoincrement' => true,
				'unsigned' => true,
			)
		);

		$table->addColumn( 'name', Type::STRING, array( 'length' => 255 ) );
		$table->addColumn( 'awesomeness', Type::INTEGER, array( 'default' => 9001 ) );

		$table->setPrimaryKey( array( 'row_id' ) );
		$table->addIndex( array( 'name' ) );

		return $table;
	}

	public function testWhenPreviousQueryWasNotInsert_getInsertIdReturnsZero() {
		$this->assertSame( 0, $this->queryInterface->getInsertId() );
	}

	public function testCanInsertAndObtainInsertId() {
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );
		$this->assertSame( 1, $this->queryInterface->getInsertId() );
	}

	public function testCanSelectInsertedRow() {
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );

		$results = $this->queryInterface->select(
			self::TABLE_NAME,
			array( 'name' ),
			array( 'row_id' => 1 )
		);

		$this->assertSame(
			array( array( 'name' => 'maru' ) ),
			iterator_to_array( $results )
		);
	}

	public function testSelectingNonExistentRowResultsInEmptyIterator() {
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );

		$results = $this->queryInterface->select(
			self::TABLE_NAME,
			array( 'name' ),
			array( 'name' => 'Ceiling cat' )
		);

		$this->assertSame(
			array(),
			iterator_to_array( $results )
		);
	}

	public function testUpdateChangesExistingValues() {
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );

		$this->queryInterface->update(
			self::TABLE_NAME,
			array( 'name' => 'Maru' ),
			array( 'name' => 'maru' )
		);

		$results = $this->queryInterface->select(
			self::TABLE_NAME,
			array( 'name' ),
			array( 'row_id' => 1 )
		);

		$this->assertSame(
			array( array( 'name' => 'Maru' ) ),
			iterator_to_array( $results )
		);
	}

	public function testCanSelectWithoutWhereConditions() {
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'Ceiling cat' ) );

		$results = $this->queryInterface->select(
			self::TABLE_NAME,
			array( 'name' ),
			array()
		);

		$this->assertSame(
			array( array( 'name' => 'maru' ), array( 'name' => 'Ceiling cat' ) ),
			iterator_to_array( $results )
		);
	}

	public function testCanSelectWithMultipleWhereConditions() {
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );

		$results = $this->queryInterface->select(
			self::TABLE_NAME,
			array( 'row_id' ),
			array( 'name' => 'maru', 'row_id' => 1 )
		);

		$this->assertSame(
			array( array( 'row_id' => '1' ) ),
			iterator_to_array( $results )
		);
	}

	public function testTableExists() {
		$this->assertTrue( $this->queryInterface->tableExists( self::TABLE_NAME ) );
		$this->assertFalse( $this->queryInterface->tableExists( 'non_existent_table' ) );
	}

	public function testCanDeleteExistingRows() {
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );
		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'Ceiling cat' ) );

		$this->queryInterface->delete(
			self::TABLE_NAME,
			array( 'name' => 'maru' )
		);

		$results = $this->queryInterface->select(
			self::TABLE_NAME,
			array( 'name' ),
			array()
		);

		$this->assertSame(
			array( array( 'name' => 'Ceiling cat' ) ),
			iterator_to_array( $results )
		);
	}

	// https://github.com/doctrine/dbal/pull/722
//	public function testCanDeleteWithoutWhereConditions() {
//		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'maru' ) );
//		$this->queryInterface->insert( self::TABLE_NAME, array( 'name' => 'Ceiling cat' ) );
//
//		$this->queryInterface->delete(
//			self::TABLE_NAME,
//			array()
//		);
//
//		$results = $this->queryInterface->select(
//			self::TABLE_NAME,
//			array( 'name' ),
//			array()
//		);
//
//		$this->assertSame(
//			array(),
//			iterator_to_array( $results )
//		);
//	}

}

