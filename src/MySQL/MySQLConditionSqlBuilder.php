<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\ValueEscaper;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLConditionSqlBuilder {

	private $escaper;

	public function __construct( ValueEscaper $escaper ) {
		$this->escaper = $escaper;
	}

	public function getConditionSql( array $conditions ) {
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

		if ( is_array( $value ) ) {
			return $this->getInClause( $key, $value );
		}

		return $this->getEqualitySql( $key, $value );
	}

	protected function getInClause( $field, array $values ) {
		$escapedValues = array();

		foreach ( $values as $value ) {
			$escapedValues[] = $this->escaper->getEscapedValue( $value );
		}

		return $field . ' IN (' . implode( ', ', $escapedValues ) . ')';
	}

	protected function getEqualitySql( $field, $value ) {
		return $field . '=' . $this->escaper->getEscapedValue( $value );
	}

}
