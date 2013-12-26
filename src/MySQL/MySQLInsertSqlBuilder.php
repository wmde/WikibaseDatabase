<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\InsertSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLInsertSqlBuilder implements InsertSqlBuilder {

	/**
	 * @see InsertSqlBuilder::getInsertSql
	 *
	 * @param string $tableName
	 * @param array $values The array keys are the field names
	 *
	 * @return string
	 */
	public function getInsertSql( $tableName, array $values ) {
		// TODO:implement
	}

}