<?php

namespace Wikibase\Database\Tests\MWDB;

use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MWDB\ExtendedSQLiteAbstraction;

/**
 * @covers Wikibase\Database\MWDB\ExtendedSQLiteAbstraction
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
class ExtendedSQLiteAbstractionTest extends ExtendedAbstractionTest {

	protected function setUp() {
		if ( !function_exists( 'wfGetDB' ) || wfGetDB( DB_SLAVE )->getType() !== 'sqlite' ) {
			$this->markTestSkipped( 'Can only run the ExtendedSQLiteAbstractionTest when MediaWiki is using SQLite' );
		}

		parent::setUp();
	}

	/**
	 * @see ExtendedAbstractionTest::newInstance
	 *
	 * @return ExtendedSQLiteAbstraction
	 */
	protected function newInstance() {
		return new ExtendedSQLiteAbstraction( new LazyDBConnectionProvider( DB_MASTER ) );
	}

}
