<?php

namespace Wikibase\Database\Tests\SQLite;

use Wikibase\Database\SQLite\SQLiteUnEscaper;

/**
 * @covers Wikibase\Database\SQLite\SQLiteUnEscaper
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 * @group UnEscaper
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteUnEscaperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider stringProvider
	 */
	public function testCanUnEscape( $before, $after ){
		$unescaper = new SQLiteUnEscaper();
		$newStr = $unescaper->getUnEscapedIdentifier( $before );
		$this->assertEquals( $after, $newStr );
	}

	public function stringProvider(){
		$cases = array();

		$cases[] = array( '"string"', 'string' );
		$cases[] = array( '"foo""bar"', 'foo"bar' );
		$cases[] = array( '"/foo ""bar"" baz/"', '/foo "bar" baz/' );

		return $cases;
	}

}