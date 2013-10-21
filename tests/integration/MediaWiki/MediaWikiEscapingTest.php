<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\MediaWiki\MediaWikiEscaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Integration
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiEscapingTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider valueEscapingProvider
	 */
	public function testValueEscaping( $input, $expectedOutput ) {
		$escaper = new MediaWikiEscaper( wfGetDB( DB_SLAVE ) );
		$output = $escaper->getEscapedValue( $input );

		$this->assertEquals(
			$expectedOutput,
			$output
		);
	}

	public function valueEscapingProvider() {
		return array(
			array( 'foo', "'foo'" ),
			array( '', "''" ),
			array( '42', "'42'" ),
			array( 42, "'42'" ),
		);
	}

}
