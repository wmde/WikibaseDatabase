<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\MySQL\MySQLConditionSqlBuilder;
use Wikibase\Database\MySQL\MySQLDeleteSqlBuilder;
use Wikibase\Database\MySQL\MySQLFieldSqlBuilder;
use Wikibase\Database\MySQL\MySQLInsertSqlBuilder;
use Wikibase\Database\MySQL\MySQLSelectSqlBuilder;
use Wikibase\Database\MySQL\MySQLTableSqlBuilder;
use Wikibase\Database\MySQL\MySQLUpdateSqlBuilder;
use Wikibase\Database\NullTableNameFormatter;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\SQLite\SQLiteFieldSqlBuilder;
use Wikibase\Database\SQLite\SQLiteIndexSqlBuilder;
use Wikibase\Database\SQLite\SQLiteTableSqlBuilder;

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

	/**
	 * @param string $dbName
	 * @return TableBuilder
	 */
	public function newMySQLTableBuilder( $dbName ) {
		$escaper = new PDOEscaper( $this->pdo );
		$tableNameFormatter = new NullTableNameFormatter();

		return new PDOTableBuilder(
			$this->pdo,
			new MySQLTableSqlBuilder(
				$dbName,
				$escaper,
				$tableNameFormatter,
				new MySQLFieldSqlBuilder( $escaper )
			),
			$tableNameFormatter,
			$escaper
		);
	}

	/**
	 * @return TableBuilder
	 */
	public function newSQLiteTableBuilder() {
		$escaper = new PDOEscaper( $this->pdo );
		$tableNameFormatter = new NullTableNameFormatter();

		return new PDOTableBuilder(
			$this->pdo,
			new SQLiteTableSqlBuilder(
				$escaper,
				$tableNameFormatter,
				new SQLiteFieldSqlBuilder( $escaper ),
				new SQLiteIndexSqlBuilder( $escaper, $tableNameFormatter )
			),
			$tableNameFormatter,
			$escaper
		);
	}

}
