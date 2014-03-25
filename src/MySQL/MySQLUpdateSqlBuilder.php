<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\Escaper;
use Wikibase\Database\QueryInterface\UpdateSqlBuilder;
use Wikibase\Database\TableNameFormatter;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLUpdateSqlBuilder implements UpdateSqlBuilder {

	private $escaper;
	private $tableNameFormatter;
	private $conditionBuilder;

	public function __construct( Escaper $escaper, TableNameFormatter $tableNameFormatter,
		MySQLConditionSqlBuilder $conditionBuilder ) {

		$this->escaper = $escaper;
		$this->tableNameFormatter = $tableNameFormatter;
		$this->conditionBuilder = $conditionBuilder;
	}

	/**
	 * @see UpdateSqlBuilder::getUpdateSql
	 *
	 * @param string $tableName
	 * @param array $values The array keys are the field names
	 * @param array $conditions
	 *
	 * @return string
	 */
	public function getUpdateSql( $tableName, array $values, array $conditions ) {
		if ( empty( $values ) ) {
			return '';
		}

		return $this->getUpdateClause( $tableName )
			. $this->getSetClause( $values )
			. $this->getWhereClause( $conditions );
	}

	private function getUpdateClause( $tableName ) {
		return 'UPDATE ' . $this->escaper->getEscapedIdentifier(
			$this->tableNameFormatter->formatTableName( $tableName )
		);
	}

	private function getSetClause( $values ) {
		$updateParts = array();

		foreach ( $values as $fieldName => $fieldValue ) {
			$updateParts[] = $this->getSetFieldSql( $fieldName, $fieldValue );
		}

		return ' SET ' . implode( ', ', $updateParts );
	}

	private function getSetFieldSql( $fieldName, $fieldValue ) {
		return $this->escaper->getEscapedIdentifier( $fieldName )
			. '='
			. $this->escaper->getEscapedValue( $fieldValue );
	}

	private function getWhereClause( $conditions ) {
		if ( empty( $conditions ) ) {
			return '';
		}

		return ' WHERE ' . $this->conditionBuilder->getConditionSql( $conditions );
	}

}
