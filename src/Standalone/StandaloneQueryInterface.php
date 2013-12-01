<?php

namespace Wikibase\Database\Standalone;

use Wikibase\Database\QueryInterface\DeleteFailedException;
use Wikibase\Database\QueryInterface\InsertFailedException;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\QueryInterface\ResultIterator;
use Wikibase\Database\QueryInterface\SelectFailedException;
use Wikibase\Database\QueryInterface\UpdateFailedException;
use Wikibase\Database\QueryInterface\ValueInserter;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StandaloneQueryInterface implements QueryInterface {

	private $inserter;

	/**
	 * @since 0.2
	 */
	public function __construct( ValueInserter $inserter ) {
		$this->inserter = $inserter;
	}

	/**
	 * @see QueryInterface::tableExists
	 *
	 * @since 0.2
	 *
	 * @param string $tableName
	 *
	 * @return boolean
	 */
	public function tableExists( $tableName ) {
		// TODO
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
		$this->inserter->insert( $tableName, $values );
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
		// TODO
		throw new UpdateFailedException( $tableName, $values, $conditions );
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
		// TODO
		//throw new DeleteFailedException( $tableName, $conditions );
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
	 * @return ResultIterator
	 * @throws SelectFailedException
	 */
	public function select( $tableName, array $fields, array $conditions, array $options = array() ) {
		// TODO
		//throw new SelectFailedException( $tableName, $fields, $conditions );
	}

}


