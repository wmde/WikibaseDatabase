<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use RuntimeException;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\MySQL\MySQLTableDefinitionReader;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\Database\SQLite\SQLiteTableDefinitionReader;

/**
 * Builder for TableDefinitionReader objects.
 * Implemented as fluent interface.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MWTableDefinitionReaderBuilder {

	/**
	 * @var DBConnectionProvider
	 */
	protected $connectionProvider;

	/**
	 * @param DBConnectionProvider $dbConnection
	 * @return $this
	 */
	public function setConnection( DBConnectionProvider $dbConnection ) {
		$this->connectionProvider = $dbConnection;
		return $this;
	}

	/**
	 * @param QueryInterface $queryInterface
	 * @throws RuntimeException
	 * @return TableDefinitionReader
	 */
	public function getTableDefinitionReader( QueryInterface $queryInterface ) {
		$dbType = $this->connectionProvider->getConnection()->getType();

		if ( $dbType === 'mysql' ) {
			return $this->newMySQLTableDefinitionReader( $queryInterface );
		}

		if ( $dbType === 'sqlite' ) {
			return $this->newSQLiteTableDefinitionReader( $queryInterface );
		}

		throw new RuntimeException( "Cannot build a TableDefinitionReader for database type '$dbType'." );
	}

	protected function newMySQLTableDefinitionReader( QueryInterface $queryInterface ) {
		return new MySQLTableDefinitionReader( $queryInterface );
	}

	protected function newSQLiteTableDefinitionReader( QueryInterface $queryInterface ) {
		return new SQLiteTableDefinitionReader( $queryInterface );
	}

}
