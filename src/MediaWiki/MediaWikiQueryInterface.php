<?php

namespace Wikibase\Database\MediaWiki;

use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\DeleteFailedException;
use Wikibase\Database\InsertFailedException;
use Wikibase\Database\QueryInterface;
use Wikibase\Database\ResultIterator;
use Wikibase\Database\SelectFailedException;
use Wikibase\Database\TableCreationFailedException;
use Wikibase\Database\TableDefinition;
use Wikibase\Database\TableSqlBuilder;
use Wikibase\Database\UpdateFailedException;

/**
 * Implementation of the QueryInterface interface using the MediaWiki
 * database abstraction layer where possible.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiQueryInterface implements QueryInterface {

	/**
	 * @var DBConnectionProvider
	 */
	private $connectionProvider;

	/**
	 * @var TableSqlBuilder
	 */
	private $tableSqlBuilder;

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
	 * @return \DatabaseBase
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
	 */
	public function tableExists( $tableName ) {
		return $this->getDB()->tableExists( $tableName, __METHOD__ );
	}

	/**
	 * @see QueryInterface::createTable
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @throws TableCreationFailedException
	 */
	public function createTable( TableDefinition $table ) {
		$sql = $this->tableSqlBuilder->getCreateTableSql( $table );

		$success = $this->getDB()->query( $sql );

		if ( $success === false ) {
			throw new TableCreationFailedException( $table, $this->getDB()->lastQuery() );
		}
	}

	/**
	 * @see QueryInterface::dropTable
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * @return boolean Success indicator
	 */
	public function dropTable( $tableName ) {
		return $this->getDB()->dropTable( $tableName, __METHOD__ ) !== false;
	}

	/**
	 * @see QueryInterface::insert
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param array $values
	 *
	 * @throws InsertFailedException
	 */
	public function insert( $tableName, array $values ) {
		$success = $this->getDB()->insert(
			$tableName,
			$values,
			__METHOD__
		) !== false;

		if ( !$success ) {
			throw new InsertFailedException( $tableName, $values );
		}
	}

	/**
	 * @see QueryInterface::update
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param array $values
	 * @param array $conditions
	 *
	 * @throws UpdateFailedException
	 */
	public function update( $tableName, array $values, array $conditions ) {
		$success = $this->getDB()->update(
			$tableName,
			$values,
			$conditions,
			__METHOD__
		) !== false;

		if ( !$success ) {
			throw new UpdateFailedException( $tableName, $values, $conditions );
		}
	}

	/**
	 * @see QueryInterface::delete
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param array $conditions
	 *
	 * @throws DeleteFailedException
	 */
	public function delete( $tableName, array $conditions ) {
		$success = $this->getDB()->delete(
			$tableName,
			$conditions,
			__METHOD__
		) !== false;

		if ( !$success ) {
			throw new DeleteFailedException( $tableName, $conditions );
		}
	}

	/**
	 * @see QueryInterface::getInsertId
	 *
	 * @since 0.1
	 *
	 * @return int
	 */
	public function getInsertId() {
		return $this->getDB()->insertId();
	}

	/**
	 * @see QueryInterface::select
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param array $fields
	 * @param array $conditions
	 *
	 * @return ResultIterator
	 * @throws SelectFailedException
	 */
	public function select( $tableName, array $fields, array $conditions ) {
		$selectionResult = $this->getDB()->select(
			$tableName,
			 $fields,
			$conditions,
			__METHOD__
		);

		if ( $selectionResult instanceof \ResultWrapper ) {
			return new ResultIterator( iterator_to_array( $selectionResult ) );
		}

		throw new SelectFailedException( $tableName, $fields, $conditions );
	}

}


