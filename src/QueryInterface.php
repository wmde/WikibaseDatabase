<?php

namespace Wikibase\Database;

use Iterator;
use Wikibase\Database\Exception\DeleteFailedException;
use Wikibase\Database\Exception\InsertFailedException;
use Wikibase\Database\Exception\QueryInterfaceException;
use Wikibase\Database\Exception\SelectFailedException;
use Wikibase\Database\Exception\UpdateFailedException;

/**
 * Interface for objects that provide a database query service.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryInterface {

	/**
	 * Inserts the provided values into the specified table.
	 * The values are provided as an associative array in
	 * which the keys are the field names.
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param array $values
	 *
	 * @throws InsertFailedException
	 */
	public function insert( $tableName, array $values );

	/**
	 * Updates the rows that match the conditions with the provided values.
	 * The values and conditions are provided as an associative array in
	 * which the keys are the field names.
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param array $values
	 * @param array $conditions
	 *
	 * @throws UpdateFailedException
	 */
	public function update( $tableName, array $values, array $conditions );

	/**
	 * Removes the rows matching the provided conditions from the specified table.
	 * The conditions are provided as an associative array in
	 * which the keys are the field names.
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 * @param array $conditions
	 *
	 * @throws DeleteFailedException
	 */
	public function delete( $tableName, array $conditions );

	/**
	 * The ID generated for an AUTO_INCREMENT column by the previous
	 * query on success, 0 if the previous
	 * query does not generate an AUTO_INCREMENT value.
	 *
	 * @since 0.1
	 *
	 * @return int
	 * @throws QueryInterfaceException
	 */
	public function getInsertId();

	/**
	 * Selects the specified fields from the rows that match the provided conditions.
	 *
	 * The conditions are provided as an associative array in which the keys are the field names.
	 *
	 * The optional options are provided as an array.
	 * Options are specified by using the key as the options and the value as the value
	 * Boolean options are specified by including them in the array as a string value with a numeric key.
	 *
	 * The returned iterator has an array per result row. Each field can be accessed via the
	 * array key with the name of the field.
	 *
	 * @since 0.1
	 *
	 * @param string|string[] $tableName
	 * @param string[] $fieldNames
	 * @param array $conditions
	 * @param array $options
	 *
	 * @return Iterator
	 * @throws SelectFailedException
	 */
	public function select( $tableName, array $fieldNames, array $conditions, array $options = array() );

	/**
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * @return boolean
	 * @throws QueryInterfaceException
	 */
	public function tableExists( $tableName );

}
