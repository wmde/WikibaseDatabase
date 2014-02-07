<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\IdentifierEscaper;
use Wikibase\Database\QueryInterface\DeleteSqlBuilder;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySQLDeleteSqlBuilder implements DeleteSqlBuilder {

	private $conditionBuilder;
	private $identifierEscaper;

	public function __construct( IdentifierEscaper $identifierEscaper, MySQLConditionSqlBuilder $conditionBuilder ) {
		$this->conditionBuilder = $conditionBuilder;
		$this->identifierEscaper = $identifierEscaper;
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
		$sql = 'DELETE FROM ' . $this->identifierEscaper->getEscapedIdentifier( $tableName );

		if ( !empty( $conditions ) ) {
			$sql .= ' WHERE ' . $this->conditionBuilder->getConditionSql( $conditions );
		}

		return $sql;
	}

}
