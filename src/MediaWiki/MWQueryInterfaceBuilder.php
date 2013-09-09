<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use RuntimeException;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\MySQL\MySqlTableSqlBuilder;
use Wikibase\Database\QueryInterface;
use Wikibase\Database\SQLite\SQLiteTableSqlBuilder;

/**
 * Builder for MediaWikiQueryInterface objects.
 * Implemented as fluent interface.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MWQueryInterfaceBuilder {

	/**
	 * @var DBConnectionProvider
	 */
	protected $connectionProvider;

	protected $tablePrefix;

	/**
	 * @param DBConnectionProvider $dbConnection
	 *
	 * @return $this
	 */
	public function setConnection( DBConnectionProvider $dbConnection ) {
		$this->connectionProvider = $dbConnection;
		return $this;
	}

	/**
	 * @return QueryInterface
	 */
	public function getQueryInterface() {
		return new MediaWikiQueryInterface(
			$this->connectionProvider,
			$this->getTableSqlBuilder()
		);
	}

	protected function getTableSqlBuilder() {
		$dbType = $this->connectionProvider->getConnection()->getType();

		if ( $dbType === 'mysql' ) {
			return $this->newMySqlTableSqlBuilder();
		}

		if ( $dbType === 'sqlite' ) {
			return $this->newSQLiteTableSqlBuilder();
		}

		throw new RuntimeException( "Cannot build a MediaWikiQueryInterface for database type '$dbType'." );
	}

	protected function newMySqlTableSqlBuilder() {
		return new MySqlTableSqlBuilder(
			$this->connectionProvider->getConnection()->getDBname(),
			$this->connectionProvider->getConnection()->tablePrefix(),
			$this->newEscaper(),
			$this->newTableNameFormatter()
		);
	}

	protected function newSQLiteTableSqlBuilder() {
		return new SQLiteTableSqlBuilder(
			$this->connectionProvider->getConnection()->tablePrefix(),
			$this->newEscaper(),
			$this->newTableNameFormatter()
		);
	}

	protected function newEscaper() {
		return new MediaWikiEscaper( $this->connectionProvider->getConnection() );
	}

	private function newTableNameFormatter() {
		return new MediaWikiTableNameFormatter( $this->connectionProvider  );
	}

}
