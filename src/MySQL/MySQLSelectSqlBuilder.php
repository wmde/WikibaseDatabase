<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\IdentifierEscaper;
use Wikibase\Database\QueryInterface\SelectSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLSelectSqlBuilder implements SelectSqlBuilder {

	private $identifierEscaper;
	private $conditionBuilder;

	public function __construct( IdentifierEscaper $identifierEscaper, MySQLConditionSqlBuilder $conditionBuilder ) {
		$this->identifierEscaper = $identifierEscaper;
		$this->conditionBuilder = $conditionBuilder;
	}

	/**
	 * @see SelectSqlBuilder::getSelectSql
	 *
	 * @param string|string[] $tableName
	 * @param string[] $fieldNames
	 * @param array $conditions The array keys are the field names
	 * @param array $options
	 *
	 * @return string
	 */
	public function getSelectSql( $tableName, array $fieldNames, array $conditions, array $options = array() ) {
		// TODO: implement options

		return $this->getSelectClause( $fieldNames )
				. $this->getFromClause( $tableName )
				. $this->getWhereClause( $conditions );
	}

	private function getSelectClause( $fieldNames ) {
		return 'SELECT ' . $this->getFieldListSql( $fieldNames );
	}

	private function getFieldListSql( array $fieldNames ) {
		$fieldNames = array_map(
			array( $this->identifierEscaper, 'getEscapedIdentifier' ),
			$fieldNames
		);

		return implode( ', ', $fieldNames );
	}

	private function getFromClause( $tableName ) {
		if( is_array( $tableName ) ) {
			$tableNames = array();
			foreach( $tableName as $name ) {
				$tableNames[] = $this->identifierEscaper->getEscapedIdentifier( $name );
			}
			return ' FROM ' . implode( ', ', $tableNames );
		}
		return ' FROM ' . $this->identifierEscaper->getEscapedIdentifier( $tableName );
	}

	private function getWhereClause( array $conditions ) {
		if ( empty( $conditions ) ) {
			return '';
		}

		return ' WHERE ' . $this->conditionBuilder->getConditionSql( $conditions );
	}

}