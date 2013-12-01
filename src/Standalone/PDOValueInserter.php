<?php

namespace Wikibase\Database\Standalone;

use PDO;
use Wikibase\Database\QueryInterface\InsertFailedException;
use Wikibase\Database\QueryInterface\InsertSqlBuilder;
use Wikibase\Database\QueryInterface\ValueInserter;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOValueInserter implements ValueInserter {

	private $pdo;
	private $insertBuilder;

	/**
	 * @since 0.2
	 */
	public function __construct( PDO $pdo, InsertSqlBuilder $insertBuilder ) {
		$this->pdo = $pdo;
		$this->insertBuilder = $insertBuilder;
	}

	/**
	 * @see ValueInserter::insert
	 *
	 * @param string $tableName
	 * @param array $values The array keys are the field names
	 *
	 * @return string
	 * @throws InsertFailedException
	 */
	public function insert( $tableName, array $values ) {
		$result = $this->pdo->query( $this->insertBuilder->getInsertSql( $tableName, $values ) );

		if ( $result === false ) {
			throw new InsertFailedException( $tableName, $values );
		}
	}

}