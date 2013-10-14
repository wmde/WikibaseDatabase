<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MediaWikiSchemaModifierBuilder;

/**
 * @covers Wikibase\Database\MediaWiki\MediaWikiSchemaModifierBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMediawiki
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MediaWikiSchemaModifierBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new MediaWikiSchemaModifierBuilder();
		$this->assertTrue( true );
	}

	public function testSetConnectionReturnsThis() {
		$builder = new MediaWikiSchemaModifierBuilder();

		$returnValue = $builder->setConnection( $this->getMock( 'Wikibase\Database\DBConnectionProvider' ) );

		$this->assertSame( $builder, $returnValue );
	}

	public function testSetQueryInterfaceReturnsThis() {
		$builder = new MediaWikiSchemaModifierBuilder();

		$mockQueryInterface = $this->getMockBuilder( 'Wikibase\Database\MediaWiki\MediaWikiQueryInterface' )
			->disableOriginalConstructor()
			->getMock();

		$returnValue = $builder->setQueryInterface( $mockQueryInterface );

		$this->assertSame( $builder, $returnValue );
	}

	public function databaseTypeProvider(){
		return array(
			array( 'mysql', 'DatabaseMysql' ),
			array( 'sqlite', 'DatabaseSqlite'),
		);
	}

	/**
	 * @dataProvider databaseTypeProvider
	 */
	public function testGetSchemaModifier( $type, $class ){
		$connection =  $this->getMock( $class );

		$connection->expects( $this->atLeastOnce() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );

		$connectionProvider->expects( $this->atLeastOnce() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$builder = new MediaWikiSchemaModifierBuilder();

		$schemaModifier = $builder
			->setConnection( $connectionProvider )
			->setQueryInterface( $queryInterface )
			->getSchemaModifier();

		$this->assertInstanceOf( 'Wikibase\Database\Schema\SchemaModifier', $schemaModifier );
	}

	public function testUnsupportedDbType(){
		$this->setExpectedException( 'RuntimeException', 'Cannot build a MediaWikiSchemaModifier for database type' );

		$connection =  $this->getMock( 'DatabaseMysql' );
		$connection->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( 'foobar' ) );

		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );
		$connectionProvider->expects( $this->atLeastOnce() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		$queryInterface = $this->getMockBuilder( 'Wikibase\Database\MediaWiki\MediaWikiQueryInterface' )
			->disableOriginalConstructor()
			->getMock();

		$builder = new MediaWikiSchemaModifierBuilder();

		$builder
			->setConnection( $connectionProvider )
			->setQueryInterface( $queryInterface )
			->getSchemaModifier();
	}

	public function testSQLiteNeedsQueryInterface(){
		$this->setExpectedException( 'RuntimeException', "Cannot build a MediaWikiSchemaModifier for database type 'SQLite' without queryInterface being defined" );
		$connection =  $this->getMock( 'DatabaseSqlite' );

		$connection->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( 'sqlite' ) );

		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );

		$connectionProvider->expects( $this->atLeastOnce() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		$builder = new MediaWikiSchemaModifierBuilder();

		$builder
			->setConnection( $connectionProvider )
			->getSchemaModifier();
	}
}