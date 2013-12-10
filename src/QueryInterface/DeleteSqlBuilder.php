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
	 * @return string
	 */
	public function getDeleteSql( $tableName, array $conditions );

}
