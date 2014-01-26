<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\InsertSqlBuilder;
use Wikibase\Database\ValueEscaper;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLInsertSqlBuilder implements InsertSqlBuilder {

	protected $escaper;

	public function __construct( ValueEscaper $escaper ) {
		$this->escaper = $escaper;
	}

	/**
	 * @see InsertSqlBuilder::getInsertSql
	 *
	 * @param string $tableName
	 * @param array $values The array keys are the field names
	 *
	 * @return string
	 */
	public function getInsertSql( $tableName, array $values ) {
		if ( empty( $values ) ) {
			return '';
		}

		return $this->getTablePart( $tableName )
			. ' ' . $this->getFieldPart( $values )
			. ' ' . $this->getValuesPart( $values );
	}

	protected function getTablePart( $tableName ) {
		return 'INSERT INTO ' . $tableName;
	}

	protected function getFieldPart( array $values ) {
		$fieldNames = array();

		foreach ( array_keys( $values ) as $fieldName ) {
			$fieldNames[] = $fieldName;
		}

		return '(' . implode( ', ', $fieldNames ) . ')';
	}

	protected function getValuesPart( array $values ) {
		$escapedValues = array();

		foreach ( $values as $value ) {
			$escapedValues[] = $this->escaper->getEscapedValue( $value );
		}

		return 'VALUES (' . implode( ', ', $escapedValues ) . ')';
	}

}