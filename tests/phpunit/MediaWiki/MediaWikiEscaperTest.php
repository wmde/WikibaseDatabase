<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MediaWikiEscaper;

/**
 * @covers Wikibase\Database\MediaWiki\MediaWikiEscaper
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MediaWikiEscaperTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		new MediaWikiEscaper( $this->getMock( 'DatabaseMysql' ) );
		$this->assertTrue( true );
	}

	public function testEscapeEmptyStringValue() {
		$inputString = '';

		$dbConnection =  $this->getMock( 'DatabaseMysql' );

		$dbConnection->expects( $this->once() )
			->method( 'addQuotes' )
			->with( $this->equalTo( $inputString ) )
			->will( $this->returnValue( 'foo bar baz' ) );

		$escaper = new MediaWikiEscaper( $dbConnection );

		$this->assertEquals( 'foo bar baz', $escaper->getEscapedValue( '' ) );
	}

	public function testEscapeEmptyStringIdentifier() {
		$inputString = '';

		$dbConnection =  $this->getMock( 'DatabaseMysql' );

		$dbConnection->expects( $this->once() )
			->method( 'addIdentifierQuotes' )
			->with( $this->equalTo( $inputString ) )
			->will( $this->returnValue( 'foo bar baz' ) );

		$escaper = new MediaWikiEscaper( $dbConnection );

		$this->assertEquals( 'foo bar baz', $escaper->getEscapedIdentifier( '' ) );
	}

}
