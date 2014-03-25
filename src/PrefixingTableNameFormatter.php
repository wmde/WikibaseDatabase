<?php

namespace Wikibase\Database;

use InvalidArgumentException;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PrefixingTableNameFormatter implements TableNameFormatter {

	/**
	 * @param string $prefix
	 * @throws InvalidArgumentException
	 */
	public function __construct( $prefix ) {
		if ( !is_string( $prefix ) ) {
			throw new InvalidArgumentException( '$prefix should be a string' );
		}

		$this->prefix = $prefix;
	}

	/**
	 * @see TableName::formatTableName
	 *
	 * @param string $tableName
	 *
	 * @return string
	 */
	public function formatTableName( $tableName ) {
		return $this->prefix . $tableName;
	}

}
