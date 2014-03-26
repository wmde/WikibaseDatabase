<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\MySQL\MySQLConditionSqlBuilder;
use Wikibase\Database\MySQL\MySQLDeleteSqlBuilder;
use Wikibase\Database\MySQL\MySQLInsertSqlBuilder;
use Wikibase\Database\MySQL\MySQLSelectSqlBuilder;
use Wikibase\Database\MySQL\MySQLUpdateSqlBuilder;
use Wikibase\Database\NullTableNameFormatter;
use Wikibase\Database\QueryInterface\QueryInterface;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOFactory {

	private $pdo;

	public function __construct( PDO $pdo ) {
		$this->pdo = $pdo;
	}

	/**
	 * @return QueryInterface
	 */
	public function newMySQLQueryInterface() {
		$escaper = new PDOEscaper( $this->pdo );
		$tableNameFormatter = new NullTableNameFormatter();

		$conditionBuilder = new MySQLConditionSqlBuilder( $escaper, $escaper );

		return new PDOQueryInterface(
			$this->pdo,
			new MySQLInsertSqlBuilder( $escaper, $tableNameFormatter ),
			new MySQLUpdateSqlBuilder( $escaper, $tableNameFormatter, $conditionBuilder ),
			new MySQLDeleteSqlBuilder( $escaper, $conditionBuilder ),
			new MySQLSelectSqlBuilder( $escaper, $conditionBuilder )
		);
	}

}
