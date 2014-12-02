<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\ResultWrapperWrapper;

/**
 * @covers Wikibase\Database\MediaWiki\ResultWrapperWrapper
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group WikibaseDatabaseMediaWiki
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ResultWrapperWrapperTest extends \PHPUnit_Framework_TestCase {

	public function testIterationOverWrapperReturnsOnlyArrays() {
		$resultWrapper = wfGetDB()->select( 'user', array( '*' ) );

		$this->assertContainsOnly( 'object', $resultWrapper );

		$wrapperWrapper = new ResultWrapperWrapper( $resultWrapper );

		$this->assertContainsOnly( 'array', $wrapperWrapper );

		$this->assertSameSize( $resultWrapper, $wrapperWrapper );
	}

	public function testWrapperRetainsSizeOfWrappedWrapper() {
		$resultWrapper = wfGetDB()->select( 'user', array( '*' ) );
		$wrapperWrapper = new ResultWrapperWrapper( $resultWrapper );

		$this->assertSameSize( $resultWrapper, $wrapperWrapper );
	}

}
