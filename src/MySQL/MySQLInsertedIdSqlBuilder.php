<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\InsertedIdSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLInsertedIdSqlBuilder implements InsertedIdSqlBuilder {

	/**
	 * @see InsertedIdSqlBuilder::getInsertedIdSql
	 *
	 * @return string
	 */
	public function getSqlToGetTheInsertedId() {
		// TODO: Implement getSqlToGetTheInsertedId() method.
	}

}