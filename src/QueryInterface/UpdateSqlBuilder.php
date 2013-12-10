<?php

namespace Wikibase\Database\QueryInterface;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface UpdateSqlBuilder {

	/**
	 * @param string $tableName
	 * @param array $values The array keys are the field names
	 * @param array $conditions
	 *
	 * @return string
	 */
	public function getUpdateSql( $tableName, array $values, array $conditions );

}
