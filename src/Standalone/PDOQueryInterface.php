<?php

namespace Wikibase\Database\Standalone;

use Iterator;
use PDO;
use Symfony\Component\Process\Exception\RuntimeException;
use Wikibase\Database\QueryInterface\DeleteFailedException;
use Wikibase\Database\QueryInterface\DeleteSqlBuilder;
use Wikibase\Database\QueryInterface\InsertFailedException;
use Wikibase\Database\QueryInterface\InsertSqlBuilder;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\QueryInterface\SelectFailedException;
use Wikibase\Database\QueryInterface\SelectSqlBuilder;
use Wikibase\Database\QueryInterface\UpdateFailedException;
use Wikibase\Database\QueryInterface\UpdateSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOQueryInterface implements QueryInterface {

	private $pdo;
	private $insertBuilder;
	private $updateBuilder;
	private $deleteBuilder;

	/**
	 * @since 0.2
	 */
	public function __construct( PDO $pdo, InsertSqlBuilder $insertBuilder, UpdateSqlBuilder $updateBuilder,
		DeleteSqlBuilder $deleteBuilder, SelectSqlBuilder $selectBuilder ) {

		$this->pdo = $pdo;
		$this->insertBuilder = $insertBuilder;
		$this->updateBuilder = $updateBuilder;
		$this->deleteBuilder = $deleteBuilder;
		$this->selectBuilder = $selectBuilder;
	}

	/**
	 * @see QueryInterface::tableExists
	 *
	 * @since 0.2
	 *
	 * @param string $tableName
	 *
	 * @return boolean
	 * @throws RuntimeException
	 */
	public function tableExists( $tableName ) {
		throw new RuntimeException( 'Not implemented: PDOQueryInterface::tableExists' );
	}

	/**
	 * @see QueryInterface::insert
	 *
	 * @since 0.2
	 *
	 * @param string $tableName
	 * @param array $values
	 *
	 * @throws InsertFailedException
	 */
	public function insert( $tableName, array $values ) {
		$result = $this->pdo->query( $this->insertBuilder->getInsertSql( $tableName, $values ) );

		if ( $result === false ) {
			throw new InsertFailedException( $tableName, $values );
		}
	}

	/**
	 * @see QueryInterface::update
	 *
	 * @since 0.2
	 *
	 * @param string $tableName
	 * @param array $values
	 * @param array $conditions
	 *
	 * @throws UpdateFailedException
	 */
	public function update( $tableName, array $values, array $conditions ) {
		$result = $this->pdo->query( $this->updateBuilder->getUpdateSql( $tableName, $values, $conditions ) );

		if ( $result === false ) {
			throw new UpdateFailedException( $tableName, $values, $conditions );
		}
	}

	/**
	 * @see QueryInterface::delete
	 *
	 * @since 0.2
	 *
	 * @param string $tableName
	 * @param array $conditions
	 *
	 * @throws DeleteFailedException
	 */
	public function delete( $tableName, array $conditions ) {
		$result = $this->pdo->query( $this->deleteBuilder->getDeleteSql( $tableName, $conditions ) );

		if ( $result === false ) {
			throw new DeleteFailedException( $tableName, $conditions );
		}
	}

	/**
	 * @see QueryInterface::select
	 *
	 * @since 0.2
	 *
	 * @param string $tableName
	 * @param array $fields
	 * @param array $conditions
	 * @param array $options
	 *
	 * @return Iterator
	 * @throws SelectFailedException
	 */
	public function select( $tableName, array $fields, array $conditions, array $options = array() ) {
		$result = $this->pdo->query( $this->selectBuilder->getSelectSql( $tableName, $fields, $conditions ) );

		if ( $result === false ) {
			throw new SelectFailedException( $tableName, $fields, $conditions );
		}

		return new PDOResult( $result );
	}

	/**
	 * @see QueryInterface::getInsertId
	 *
	 * @since 0.2
	 *
	 * @return int
	 */
	public function getInsertId() {
		// TODO
	}

}


