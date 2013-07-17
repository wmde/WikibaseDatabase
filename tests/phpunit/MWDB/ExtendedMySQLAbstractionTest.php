<?php

namespace Wikibase\Database\Tests\MWDB;

use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MWDB\ExtendedMySQLAbstraction;

/**
 * @covers Wikibase\Database\MWDB\ExtendedMySQLAbstraction
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseDatabaseTest
 *
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Database
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ExtendedMySQLAbstractionTest extends ExtendedAbstractionTest {

	protected function setUp() {
		if ( !function_exists( 'wfGetDB' ) || wfGetDB( DB_SLAVE )->getType() !== 'mysql' ) {
			$this->markTestSkipped( 'Can only run the ExtendedMySQLAbstractionTest when MediaWiki is using MySQL' );
		}

		parent::setUp();
	}

	/**
	 * @see ExtendedAbstractionTest::newInstance
	 *
	 * @return ExtendedMySQLAbstraction
	 */
	protected function newInstance() {
		return new ExtendedMySQLAbstraction( new LazyDBConnectionProvider( DB_MASTER ) );
	}

}
