<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\Schema\TableCreationFailedException;
use Wikibase\Database\Schema\TableDeletionFailedException;
use Wikibase\Database\Schema\TableSqlBuilder;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiTableBuilder implements TableBuilder {

	/**
	 * @var DBConnectionProvider
	 */
	private $connectionProvider;

	/**
	 * @var TableSqlBuilder
	 */
	protected $tableSqlBuilder;

	/**
	 * @since 0.1
	 *
	 * @param DBConnectionProvider $connectionProvider
	 * @param TableSqlBuilder $tableSqlBuilder
	 */
	public function __construct( DBConnectionProvider $connectionProvider, TableSqlBuilder $tableSqlBuilder ) {
		$this->connectionProvider = $connectionProvider;
		$this->tableSqlBuilder = $tableSqlBuilder;
	}

	/**
	 * @return DatabaseBase
	 */
	private function getDB() {
		return $this->connectionProvider->getConnection();
	}

	/**
	 * @see QueryInterface::tableExists
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * @return boolean
	 * @throws InvalidArgumentException
	 */
	public function tableExists( $tableName ) {
		if ( !is_string( $tableName ) ) {
			throw new InvalidArgumentException( '$tableName should be a string' );
		}
		
		return $this->getDB()->tableExists( $tableName, __METHOD__ );
	}


	/**
	 * @see TableBuilder::createTable
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @throws TableCreationFailedException
	 */
	public function createTable( TableDefinition $table ) {
		$sql = $this->tableSqlBuilder->getCreateTableSql( $table );

		foreach( explode( PHP_EOL, $sql ) as $query ) {
			$success = $this->getDB()->query( $query );

			if ( $success === false ) {
				throw new TableCreationFailedException( $table, $this->getDB()->lastError() );
			}
		}

	}

	/**
	 * @see TableBuilder::dropTable
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * @throws TableDeletionFailedException
	 */
	public function dropTable( $tableName ) {
		$success = $this->getDB()->dropTable( $tableName, __METHOD__ );

		if ( $success === false ) {
			throw new TableDeletionFailedException( $tableName, $this->getDB()->lastError() );
		}
	}

}
