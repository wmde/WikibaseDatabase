<?php

namespace Wikibase\Database\QueryInterface;

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
	 * @throw DeleteFailedException
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
	 */
	public function getInsertId();

	/**
	 * Selects the specified fields from the rows that match the provided conditions.
	 * The conditions are provided as an associative array in
	 * which the keys are the field names.
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
	public function select( $tableName, array $fields, array $conditions );

}
