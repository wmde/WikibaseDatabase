<?php

namespace Wikibase\Database\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use Iterator;
use Wikibase\Database\Exception\DeleteFailedException;
use Wikibase\Database\Exception\InsertFailedException;
use Wikibase\Database\Exception\QueryInterfaceException;
use Wikibase\Database\Exception\SelectFailedException;
use Wikibase\Database\Exception\UpdateFailedException;
use Wikibase\Database\QueryInterface;

/**
 * Implementation of the QueryInterface interface using Doctrine DBAL.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DBALQueryInterface implements QueryInterface {

	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @since 0.1
	 *
	 * @param Connection $connection
	 */
	public function __construct( Connection $connection ) {
		$this->connection = $connection;
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
		return $this->connection->getSchemaManager()->tablesExist( array( $tableName ) );
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
			$this->connection->insert( $tableName, $values );
		}
		catch ( DBALException $ex ) {
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
			$this->connection->update( $tableName, $values, $conditions );
		}
		catch ( DBALException $ex ) {
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
			$this->connection->delete( $tableName, $conditions );
		}
		catch ( DBALException $ex ) {
			throw new DeleteFailedException( $tableName, $conditions, $ex->getMessage(), $ex );
		}
	}

	/**
	 * @see QueryInterface::getInsertId
	 *
	 * @since 0.1
	 *
	 * @return int
	 * @throws QueryInterfaceException
	 */
	public function getInsertId() {
		try {
			return (int)$this->connection->lastInsertId();
		}
		catch ( DBALException $ex ) {
			throw new QueryInterfaceException( 'Could not obtain the last inserted ID', 0, $ex );
		}
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
	 * @return Iterator
	 * @throws SelectFailedException
	 */
	public function select( $tableName, array $fields, array $conditions, array $options = array() ) {
		$queryBuilder = new QueryBuilder( $this->connection );

		$wherePredicates = array();

		foreach ( $conditions as $columnName => $columnValue ) {
			$wherePredicates[] = 't.' . $columnName . ' = :' . $columnName;
			$queryBuilder->setParameter( ':' . $columnName, $columnValue );
		}

		$queryBuilder
			->select( array_map(
				function( $columnName ) {
					return 't.' . $columnName;
				},
				$fields
			) )
			->from( $tableName, 't' );

		if ( $wherePredicates !== array() ) {
			$queryBuilder->where( implode( ' AND ', $wherePredicates ) );
		}

		// TODO: handle $options

		try {
			return $queryBuilder->execute();
		}
		catch ( DBALException $ex ) {
			throw new SelectFailedException( $tableName, $fields, $conditions, $ex->getMessage(), $ex );
		}
	}

}


