<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MWTableDefinitionReaderBuilder;

/**
 * @covers Wikibase\Database\MediaWiki\MWTableDefinitionReaderBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMediawiki
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MWTableDefinitionReaderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new MWTableDefinitionReaderBuilder();
		$this->assertTrue( true );
	}

	public function testSetConnectionReturnsThis() {
		$builder = new MWTableDefinitionReaderBuilder();

		$returnValue = $builder->setConnection( $this->getMock( 'Wikibase\Database\DBConnectionProvider' ) );

		$this->assertSame( $builder, $returnValue );
	}

	public function testGetDefinitionReader() {
		$connection =  $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( 'mysql' ) );

		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );

		$connectionProvider->expects( $this->atLeastOnce() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$builder = new MWTableDefinitionReaderBuilder();

		$tableDefinitionReader = $builder->setConnection( $connectionProvider )->getTableDefinitionReader( $queryInterface );

		$this->assertInstanceOf( 'Wikibase\Database\Schema\TableDefinitionReader', $tableDefinitionReader );
	}

	public function testUnsupportedDbType(){
		$this->setExpectedException( 'RuntimeException', 'Cannot build a TableDefinitionReader for database type' );

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

		$builder = new MWTableDefinitionReaderBuilder();
		$builder->setConnection( $connectionProvider )->getTableDefinitionReader( $queryInterface );
	}

}