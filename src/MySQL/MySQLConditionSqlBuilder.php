<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\IdentifierEscaper;
use Wikibase\Database\ValueEscaper;

/**
 * This class is tested via MySQLDeleteSqlBuilderTest.
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLConditionSqlBuilder {

	private $escaper;
	private $identifierEscaper;

	public function __construct( ValueEscaper $escaper, IdentifierEscaper $identifierEscaper ) {
		$this->escaper = $escaper;
		$this->identifierEscaper = $identifierEscaper;
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

		return $this->identifierEscaper->getEscapedIdentifier( $field )
			. ' IN (' . implode( ', ', $escapedValues ) . ')';
	}

	protected function getEqualitySql( $field, $value ) {
		return
			$this->identifierEscaper->getEscapedIdentifier( $field )
			. '='
			. $this->escaper->getEscapedValue( $value );
	}

}
