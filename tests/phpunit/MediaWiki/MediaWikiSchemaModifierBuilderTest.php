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

	public function testGetSchemaModifier(){
		$connection =  $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( 'mysql' ) );

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

}