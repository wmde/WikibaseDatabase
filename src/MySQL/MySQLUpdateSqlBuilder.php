<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\UpdateSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLUpdateSqlBuilder implements UpdateSqlBuilder {

	/**
	 * @see UpdateSqlBuilder::getUpdateSql
	 *
	 * @param string $tableName
	 * @param array $values The array keys are the field names
	 * @param array $conditions
	 *
	 * @return string
	 */
	public function getUpdateSql( $tableName, array $values, array $conditions ) {
		// TODO: Implement getUpdateSql() method.
	}

}