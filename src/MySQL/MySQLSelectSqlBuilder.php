<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\SelectSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLSelectSqlBuilder implements SelectSqlBuilder {

	/**
	 * @see SelectSqlBuilder::getSelectSql
	 *
	 * @param string $tableName
	 * @param string[] $fieldNames
	 * @param array $conditions The array keys are the field names
	 * @param array $options
	 *
	 * @return string
	 */
	public function getSelectSql( $tableName, array $fieldNames, array $conditions, array $options = array() ) {
		// TODO: Implement getSelectSql() method.
	}

}