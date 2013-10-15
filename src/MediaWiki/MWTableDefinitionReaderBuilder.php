<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use RuntimeException;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\MySQL\MySQLTableDefinitionReader;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\Database\SQLite\SQLiteTableDefinitionReader;
use Wikibase\Database\SQLite\SQLiteUnEscaper;

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
	 * @var QueryInterface
	 */
	protected $queryInterface;

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
	 * @return $this
	 */
	public function setQueryInterface( QueryInterface $queryInterface ) {
		$this->queryInterface = $queryInterface;
		return $this;
	}

	/**
	 * @throws RuntimeException
	 * @return TableDefinitionReader
	 */
	public function getTableDefinitionReader() {
		$dbType = $this->connectionProvider->getConnection()->getType();

		if ( $dbType === 'mysql' ) {
			return $this->newMySQLTableDefinitionReader();
		}

		if ( $dbType === 'sqlite' ) {
			return $this->newSQLiteTableDefinitionReader();
		}

		throw new RuntimeException( "Cannot build a TableDefinitionReader for database type '$dbType'." );
	}

	protected function newMySQLTableDefinitionReader() {
		return new MySQLTableDefinitionReader(
			$this->queryInterface,
			new MediaWikiTableNameFormatter( $this->connectionProvider )
		);
	}

	protected function newSQLiteTableDefinitionReader() {
		return new SQLiteTableDefinitionReader(
			$this->queryInterface,
			new SQLiteUnEscaper()
		);
	}

}
