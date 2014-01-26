<?php

namespace Wikibase\Database\QueryInterface;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface DeleteSqlBuilder {

	/**
	 * @param string $tableName
	 * @param array $conditions
	 *
	 * The conditions can be provided as stings, in which case
	 * they are used as-is. They can also be provided as key value
	 * pairs, where the (array) field key is the field name. In this
	 * case an equality condition is build, and the value is escaped.
	 * This value can also be an array, in which case they values
	 * are treated as disjunction and each escaped.
	 *
	 * @return string
	 */
	public function getDeleteSql( $tableName, array $conditions );

}
