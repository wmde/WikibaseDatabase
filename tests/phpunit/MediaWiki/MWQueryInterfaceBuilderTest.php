<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MWQueryInterfaceBuilder;

/**
 * @covers Wikibase\Database\MediaWiki\MWQueryInterfaceBuilder
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
class MWQueryInterfaceBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new MWQueryInterfaceBuilder();
		$this->assertTrue( true );
	}

	public function testSetConnectionReturnsThis() {
		$builder = new MWQueryInterfaceBuilder();

		$returnValue = $builder->setConnection( $this->getMock( 'Wikibase\Database\DBConnectionProvider' ) );

		$this->assertSame( $builder, $returnValue );
	}

	public function testGetQueryInterface() {
		$connection =  $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( 'mysql' ) );

		$connection->expects( $this->once() )
			->method( 'getDBname' )
			->will( $this->returnValue( 'dbName' ) );

		$connection->expects( $this->once() )
			->method( 'tablePrefix' )
			->will( $this->returnValue( 'prefix_' ) );

		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );

		$connectionProvider->expects( $this->atLeastOnce() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		$builder = new MWQueryInterfaceBuilder();

		$queryInterface = $builder->setConnection( $connectionProvider )->getQueryInterface();

		$this->assertInstanceOf( 'Wikibase\Database\QueryInterface', $queryInterface );
	}

}
