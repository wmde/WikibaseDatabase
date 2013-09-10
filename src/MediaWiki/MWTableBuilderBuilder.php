<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use RuntimeException;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\MySQL\MySqlTableSqlBuilder;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\SQLite\SQLiteTableSqlBuilder;

/**
 * Builder for MediaWikiTableBuilder objects.
 * Implemented as fluent interface.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MWTableBuilderBuilder {

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
	 * @return TableBuilder
	 */
	public function getTableBuilder() {
		return new MediaWikiTableBuilder(
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
			$this->newEscaper(),
			$this->newTableNameFormatter()
		);
	}

	protected function newSQLiteTableSqlBuilder() {
		return new SQLiteTableSqlBuilder(
			$this->newEscaper(),
			$this->newTableNameFormatter()
		);
	}

	protected function newEscaper() {
		return new MediaWikiEscaper( $this->connectionProvider->getConnection() );
	}

	private function newTableNameFormatter() {
		return new MediaWikiTableNameFormatter( $this->connectionProvider );
	}

}
