<?php

namespace Wikibase\Database\MediaWiki;

use RuntimeException;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\MySQL\MySQLSchemaSqlBuilder;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;
use Wikibase\Database\Schema\SchemaModifier;
use Wikibase\Database\SQLite\SQLiteSchemaSqlBuilder;

/**
 * Builder for SchemaModifier objects.
 * Implemented as fluent interface.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MediaWikiSchemaModifierBuilder {

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
	 *
	 * @return $this
	 */
	public function setConnection( DBConnectionProvider $dbConnection ) {
		$this->connectionProvider = $dbConnection;
		return $this;
	}

	/**
	 * @param QueryInterface $queryInterface
	 *
	 * @return $this
	 */
	public function setQueryInterface( QueryInterface $queryInterface ) {
		$this->queryInterface = $queryInterface;
		return $this;
	}

	/**
	 * @return SchemaModifier
	 */
	public function getSchemaModifier() {
		return new MediaWikiSchemaModifier(
			$this->connectionProvider,
			$this->getSchemaModificationSqlBuilder()
		);
	}

	/**
	 * @throws RuntimeException
	 * @return SchemaModificationSqlBuilder
	 */
	private function getSchemaModificationSqlBuilder() {
		$dbType = $this->connectionProvider->getConnection()->getType();

		if ( $dbType === 'mysql' ) {
			return $this->newMySQLSchemaModificationSqlBuilder();
		}

		if ( $dbType === 'sqlite' ) {
			return $this->newSQLiteSchemaModificationSqlBuilder();
		}

		throw new RuntimeException( "Cannot build a MediaWikiSchemaModifier for database type '$dbType'." );
	}

	private function newMySQLSchemaModificationSqlBuilder() {
		return new MySQLSchemaSqlBuilder( $this->newEscaper(), $this->newTableNameFormatter() );
	}

	private function newSQLiteSchemaModificationSqlBuilder() {
		if( !$this->queryInterface instanceof QueryInterface ){
			throw new RuntimeException( "Cannot build a MediaWikiSchemaModifier for database type 'SQLite' without queryInterface being defined" );
		}
		$tableDefinitionReaderBuilder = new MWTableDefinitionReaderBuilder();
		$tableDefinitonReader = $tableDefinitionReaderBuilder
			->setConnection( $this->connectionProvider )
			->setQueryInterface( $this->queryInterface )
			->getTableDefinitionReader();
		return new SQLiteSchemaSqlBuilder( $this->newEscaper(), $this->newTableNameFormatter(), $tableDefinitonReader );
	}

	private function newEscaper() {
		return new MediaWikiEscaper( $this->connectionProvider->getConnection() );
	}

	private function newTableNameFormatter() {
		return new MediaWikiTableNameFormatter( $this->connectionProvider );
	}

}