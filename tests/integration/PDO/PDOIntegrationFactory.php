<?php

namespace Wikibase\Database\Tests\PDO;

use PDO;
use PHPUnit_Framework_TestCase;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOIntegrationFactory {

	const DB_NAME = 'wb_db_tests';

	public static function newPDO( PHPUnit_Framework_TestCase $testCase ) {
		try {
			return new PDO(
				'mysql:dbname=' . self::DB_NAME . ';host=localhost',
				'wb_db_tester',
				'mysql_is_evil'
			);
		}
		catch ( \PDOException $ex ) {
			$testCase->markTestSkipped(
				'Test not run, presumably the database is not set up: ' . $ex->getMessage()
			);
		}
	}

}