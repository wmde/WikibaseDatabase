<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\DeleteSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLDeleteSqlBuilder implements DeleteSqlBuilder {

	/**
	 * @see DeleteSqlBuilder::getDeleteSql
	 *
	 * @param string $tableName
	 * @param array $conditions
	 *
	 * @return string
	 */
	public function getDeleteSql( $tableName, array $conditions ) {
		// TODO: Implement getDeleteSql() method.
	}

}