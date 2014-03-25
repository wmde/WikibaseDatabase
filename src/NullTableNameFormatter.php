<?php

namespace Wikibase\Database;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NullTableNameFormatter implements TableNameFormatter {

	/**
	 * @see TableName::formatTableName
	 *
	 * @param string $tableName
	 *
	 * @return string
	 */
	public function formatTableName( $tableName ) {
		return $tableName;
	}

}
