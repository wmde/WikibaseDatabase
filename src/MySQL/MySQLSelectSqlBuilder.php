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

	public function __construct( IdentifierEscaper $identifierEscaper ) {
		$this->identifierEscaper = $identifierEscaper;
	}

	/**
	 * @see SelectSqlBuilder::getSelectSql
	 *
	 * @param string $tableName
	 * @param string[] $fieldNames
	 * @param array $conditions The array keys are the field names
	 * @param array $options
	 *
	 * @return string
	 */
	public function getSelectSql( $tableName, array $fieldNames, array $conditions, array $options = array() ) {

		return $this->getSelectClause( $fieldNames )
				. ' ' . $this->getFromClause( $tableName )
				. '';
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
		return 'FROM ' . $this->identifierEscaper->getEscapedIdentifier( $tableName );
	}

}