<?php

namespace Wikibase\Database;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface TableNameFormatter {

	/**
	 * Does any formatting and escaping of a table name (as for instance obtained from a TableDefinition)
	 * which needs to be done before using it in an SQL statement.
	 *
	 * @param string $tableName
	 *
	 * @return string
	 */
	public function formatTableName( $tableName );

}
