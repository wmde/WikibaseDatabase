<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\Schema\TableCreationFailedException;
use Wikibase\Database\Schema\TableDeletionFailedException;
use Wikibase\Database\Schema\TableSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOTableBuilder implements TableBuilder {

	protected $pdo;
	protected $tableSqlBuilder;

	public function __construct( PDO $pdo, TableSqlBuilder $tableSqlBuilder ) {
		$this->pdo = $pdo;
		$this->tableSqlBuilder = $tableSqlBuilder;
	}

	/**
	 * @see TableBuilder::createTable
	 *
	 * @param TableDefinition $table
	 *
	 * @throws TableCreationFailedException
	 */
	public function createTable( TableDefinition $table ) {
		$result = $this->pdo->query( $this->tableSqlBuilder->getCreateTableSql( $table ) );

		if ( $result === false ) {
			throw new TableCreationFailedException( $table );
		}
	}

	/**
	 * @see TableBuilder::dropTable
	 *
	 * @param string $tableName
	 *
	 * @throws TableDeletionFailedException
	 */
	public function dropTable( $tableName ) {
		$result = $this->pdo->query( 'DROP TABLE ' . $tableName . ';' );

		if ( $result === false ) {
			throw new TableDeletionFailedException( $tableName );
		}
	}

	/**
	 * @see TableBuilder::tableExists
	 *
	 * @param string $tableName
	 *
	 * @return boolean
	 */
	public function tableExists( $tableName ) {
		$result = $this->pdo->query( 'SELECT 1 FROM ' . $tableName . ' LIMIT 1;' );
		return (bool)$result;
	}

}
