<?php

namespace Wikibase\Database\QueryInterface;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface InsertSqlBuilder {

	/**
	 * @param string $tableName
	 * @param array $values The array keys are the field names
	 *
	 * @return string
	 */
	public function getInsertSql( $tableName, array $values );

}
