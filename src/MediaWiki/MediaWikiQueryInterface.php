<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use Iterator;
use MWException;
use Wikibase\Database\Exception\DeleteFailedException;
use Wikibase\Database\Exception\InsertFailedException;
use Wikibase\Database\Exception\QueryInterfaceException;
use Wikibase\Database\Exception\SelectFailedException;
use Wikibase\Database\Exception\UpdateFailedException;
use Wikibase\Database\QueryInterface;

/**
 * Implementation of the QueryInterface interface using the MediaWiki
 * database abstraction layer where possible.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class MediaWikiQueryInterface implements QueryInterface {

	/**
	 * @var DBConnectionProvider
	 */
	private $connectionProvider;

	/**
	 * @since 0.1
	 *
	 * @param DBConnectionProvider $connectionProvider
	 */
	public function __construct( DBConnectionProvider $connectionProvider ) {
		$this->connectionProvider = $connectionProvider;
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
	 * @return bool
	 */
	public function tableExists( $tableName ) {
		return $this->getDB()->tableExists( $tableName, __METHOD__ );
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
		try {
			$result = $this->getDB()->insert( $tableName, $values, __METHOD__ );

			if ( $result === false ) {
				throw new InsertFailedException( $tableName, $values );
			}
		} catch ( MWException $ex ) {
			throw new InsertFailedException( $tableName, $values, $ex->getMessage(), $ex );
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
		try {
			$result = $this->getDB()->update( $tableName, $values, $conditions, __METHOD__ );

			if ( $result === false ) {
				throw new UpdateFailedException( $tableName, $values, $conditions );
			}
		} catch ( MWException $ex ) {
			throw new UpdateFailedException( $tableName, $values, $conditions, $ex->getMessage(), $ex );
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
		try {
			$result = $this->getDB()->delete( $tableName, $conditions, __METHOD__ );

			if ( $result === false ) {
				throw new DeleteFailedException( $tableName, $conditions );
			}
		} catch ( MWException $ex ) {
			throw new DeleteFailedException( $tableName, $conditions, $ex->getMessage(), $ex );
		}
	}

	/**
	 * @see QueryInterface::getInsertId
	 *
	 * @since 0.1
	 *
	 * @throws QueryInterfaceException
	 * @return int
	 */
	public function getInsertId() {
		$databaseBase = $this->getDB();

		if ( !method_exists( $databaseBase, 'insertId' ) ) {
			throw new QueryInterfaceException( 'Connection does not support obtain the last inserted ID' );
		}

		return (int)$databaseBase->insertId();
	}

	/**
	 * @see QueryInterface::select
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param string[] $fieldNames
	 * @param array $conditions
	 * @param array $options
	 *
	 * @throws SelectFailedException
	 * @return Iterator
	 */
	public function select( $tableName, array $fieldNames, array $conditions, array $options = array() ) {
		$selectionResult = $this->getDB()->select(
			$tableName,
			$fieldNames,
			$conditions,
			__METHOD__,
			$options
		);

		if ( $selectionResult instanceof \ResultWrapper ) {
			// TODO: change to return arrays instead of objects
			return $selectionResult;
		}

		throw new SelectFailedException( $tableName, $fieldNames, $conditions );
	}

}
