<?php

namespace Wikibase\Database\QueryInterface;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface SelectSqlBuilder {

	/**
	 * @param string|string[] $tableName
	 * @param string[] $fieldNames
	 * @param array $conditions The array keys are the field names
	 * @param array $options
	 *
	 * The optional options are provided as an array.
	 * Options are specified by using the key as the options and the value as the value
	 * Boolean options are specified by including them in the array as a string value with a numeric key.
	 *
	 * @return string
	 */
	public function getSelectSql( $tableName, array $fieldNames, array $conditions, array $options = array() );

}
