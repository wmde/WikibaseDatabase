<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MediaWikiTableNameFormatter;

/**
 * @covers Wikibase\Database\MediaWiki\MediaWikiTableNameFormatter
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiTableNameFormatterTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new MediaWikiTableNameFormatter( $this->getMock( 'Wikibase\Database\DBConnectionProvider' ) );
		$this->assertTrue( true );
	}

	public function testFormatTableName() {
		$inputName = 'foo';
		$prefix = 'prefix_';
		$outputName = $prefix . $inputName;

		$connection = $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'tableName' )
			->with( $this->equalTo( $inputName ) )
			->will( $this->returnValue( $outputName ) );

		$connectionProvider = $this->getMockConnectionProvider( $connection );

		$tableNameFormatter = new MediaWikiTableNameFormatter( $connectionProvider );

		$formattedName = $tableNameFormatter->formatTableName( $inputName );

		$this->assertEquals( $outputName, $formattedName );
	}

	protected function getMockConnectionProvider( $connection ) {
		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );

		$connectionProvider->expects( $this->once() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		$connectionProvider->expects( $this->once() )
			->method( 'releaseConnection' );

		return $connectionProvider;
	}

	public function testFormatTableNameWithExceptionFromDatabaseBase() {
		$inputName = 'foo';
		$exceptionMessage = 'foo bar baz';

		$exception = new \Exception( $exceptionMessage );

		$connection = $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'tableName' )
			->with( $this->equalTo( $inputName ) )
			->will( $this->throwException( $exception ) );

		$connectionProvider = $this->getMockConnectionProvider( $connection );

		$tableNameFormatter = new MediaWikiTableNameFormatter( $connectionProvider );

		$this->setExpectedException( 'Exception', $exceptionMessage );
		$tableNameFormatter->formatTableName( $inputName );
	}

}
