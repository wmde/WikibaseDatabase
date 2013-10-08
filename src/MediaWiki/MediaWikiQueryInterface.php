<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\QueryInterface\DeleteFailedException;
use Wikibase\Database\QueryInterface\InsertFailedException;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\QueryInterface\ResultIterator;
use Wikibase\Database\QueryInterface\SelectFailedException;
use Wikibase\Database\QueryInterface\UpdateFailedException;

/**
 * Implementation of the QueryInterface interface using the MediaWiki
 * database abstraction layer where possible.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
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
	 * @return boolean
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
	 * @param array $options
	 *
	 * @return ResultIterator
	 * @throws SelectFailedException
	 */
	public function select( $tableName, array $fields, array $conditions, array $options = array() ) {
		$selectionResult = $this->getDB()->select(
			$tableName,
			$fields,
			$conditions,
			__METHOD__,
			$options
		);

		if ( $selectionResult instanceof \ResultWrapper ) {
			return new ResultIterator( iterator_to_array( $selectionResult ) );
		}

		throw new SelectFailedException( $tableName, $fields, $conditions );
	}

}


