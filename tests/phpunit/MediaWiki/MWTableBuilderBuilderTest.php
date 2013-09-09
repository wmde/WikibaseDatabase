<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MWTableBuilderBuilder;

/**
 * @covers Wikibase\Database\MediaWiki\MWTableBuilderBuilder
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MWTableBuilderBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new MWTableBuilderBuilder();
		$this->assertTrue( true );
	}

	public function testSetConnectionReturnsThis() {
		$builder = new MWTableBuilderBuilder();

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

		$builder = new MWTableBuilderBuilder();

		$tableBuilder = $builder->setConnection( $connectionProvider )->getTableBuilder();

		$this->assertInstanceOf( 'Wikibase\Database\Schema\TableBuilder', $tableBuilder );
	}

}
