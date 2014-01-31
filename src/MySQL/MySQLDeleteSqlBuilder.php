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

	private $conditionBuilder;

	public function __construct( MySQLConditionSqlBuilder $conditionBuilder ) {
		$this->conditionBuilder = $conditionBuilder;
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
			$sql .= ' ' . $this->conditionBuilder->getConditionSql( $conditions );
		}

		return $sql;
	}

}
