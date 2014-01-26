<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\DeleteSqlBuilder;
use Wikibase\Database\ValueEscaper;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLDeleteSqlBuilder implements DeleteSqlBuilder {

	protected $escaper;

	public function __construct( ValueEscaper $escaper ) {
		$this->escaper = $escaper;
	}

	/**
	 * @see DeleteSqlBuilder::getDeleteSql
	 *
	 * @param string $tableName
	 * @param array $conditions
	 *
	 * @return string
	 */
	public function getDeleteSql( $tableName, array $conditions ) {
		$sql = 'DELETE FROM ' . $tableName;

		if ( !empty( $conditions ) ) {
			$sql .= ' ' . $this->getConditionSql( $conditions );
		}

		return $sql;
	}

	protected function getConditionSql( array $conditions ) {
		$expandedConditions = array();

		foreach ( $conditions as $key => $value ) {
			$expandedConditions[] = $this->expandCondition( $key, $value );
		}

		return 'WHERE ' . implode( ' AND ', $expandedConditions );
	}

	protected function expandCondition( $key, $value ) {
		if ( is_numeric( $key ) ) {
			return $value;
		}

		return $key . '=' . $this->escaper->getEscapedValue( $value );
	}

}