<?php

namespace Wikibase\Database;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface TableNameFormatter {

	/**
	 * Format a table name ready for use in constructing an SQL query.
	 * This includes doing transformations, such as prepending prefixes.
	 *
	 * It does NOT include escaping. The caller is responsible for using
	 * the appropriate type of escaping depending on the usage context.
	 *
	 * This method should typically always be called right before the
	 * construction of an SQL string. This makes it clear where the
	 * transmigration boundary is, and easier to spot inconsistencies.
	 *
	 * @param string $tableName
	 *
	 * @return string
	 */
	public function formatTableName( $tableName );

}
