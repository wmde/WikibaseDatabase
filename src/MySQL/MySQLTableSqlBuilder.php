<?php

namespace Wikibase\Database\MySQL;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableSqlBuilder;
use Wikibase\Database\TableNameFormatter;

/**
 * MySQL implementation of TableSqlBuilder.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MySQLTableSqlBuilder extends TableSqlBuilder {

	protected $dbName;
	protected $tableNameFormatter;
	protected $fieldSqlBuilder;

	/**
	 * @var TableDefinition
	 */
	protected $table;

	/**
	 * @param string $dbName
	 * @param Escaper $escaper
	 * @param TableNameFormatter $tableNameFormatter
	 * @param MySQLFieldSqlBuilder $fieldSqlBuilder
	 */
	public function __construct( $dbName, Escaper $escaper, TableNameFormatter $tableNameFormatter, MySQLFieldSqlBuilder $fieldSqlBuilder ) {
		$this->dbName = $dbName;
		$this->escaper = $escaper;
		$this->tableNameFormatter = $tableNameFormatter;
		$this->fieldSqlBuilder = $fieldSqlBuilder;
	}

	/**
	 * @see ExtendedAbstraction::createTable
	 * @see http://dev.mysql.com/doc/refman/5.7/en/create-table.html
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @return string
	 */
	public function getCreateTableSql( TableDefinition $table ) {
		$this->table = $table;

		$sql = $this->getCreateNameSql();
		$sql .= $this->getTheBracketsAndSqlInBetween();
		$sql .= $this->getTableOptionSql();
		$sql .= $this->getQueryEnd();

		return $sql;
	}

	protected function getCreateNameSql() {
		// $this->dbName should perhaps be escaped as an identifier.
		return 'CREATE TABLE `' . $this->dbName . '`.'
			. $this->getPreparedTableName( $this->table->getName() );
	}

	protected function getPreparedTableName( $tableName ) {
		return $this->escaper->getEscapedIdentifier(
			$this->tableNameFormatter->formatTableName( $tableName )
		);
	}

	protected function getTheBracketsAndSqlInBetween() {
		$queryParts = array();

		foreach ( $this->table->getFields() as $field ) {
			$queryParts[] = $this->fieldSqlBuilder->getFieldSQL( $field );
		}

		foreach ( $this->table->getIndexes() as $index ){
			$queryParts[] = $this->getIndexSQL( $index );
		}

		return ' (' . implode( ', ', $queryParts ) . ') ';
	}

	protected function getTableOptionSql() {
		return 'ENGINE=InnoDB, DEFAULT CHARSET=binary';
	}
		// Should the engine really be hardcoded? InnoDB is performant for concurrent modification,
		// but MyISAM may be preferable for raw query speed and bulk updates. Also, InnDB doesn't
		// support full text indexes.
		// The charset also should be configurable: using binary is safe, but using utf8 gives
		// better collation (sort order). Note however that when using utf8, comparison
		// of varchar/char/text is case insensitive per default.

	protected function getQueryEnd() {
		return ';';
	}

	/**
	 * @since 0.1
	 *
	 * @param IndexDefinition $index
	 *
	 * @return string
	 * @todo use a MySQLIndexSqlBuilder (although this creates indexes in a seperate query)
	 */
	protected function getIndexSQL( IndexDefinition $index ) {
		$sql = $this->getIndexType( $index->getType() );

		if( $index->getType() !== IndexDefinition::TYPE_PRIMARY ){
			$sql .= ' ' . $this->escaper->getEscapedIdentifier( $index->getName() );
		}

		$cols = array();
		foreach( $index->getColumns() as $columnName => $intSize ){
			$colName =  $this->escaper->getEscapedIdentifier( $columnName );
			if( $intSize !== 0 ) {
				$colName .= "({$intSize})";
			}
			$cols[] = $colName;
		}

		$sql .= ' (' . implode( ',', $cols ) . ')';

		return $sql;
	}

	/**
	 * Returns the MySQL field type for a given IndexDefinition type constant.
	 *
	 * @param string $indexType
	 *
	 * @return string
	 * @throws RuntimeException
	 * @todo use a MySQLIndexSqlBuilder (although this creates indexes in a seperate query)
	 */
	protected function getIndexType( $indexType ) {
		switch ( $indexType ) {
			case IndexDefinition::TYPE_PRIMARY:
				return 'PRIMARY KEY';
			case IndexDefinition::TYPE_INDEX:
				return 'INDEX';
			case IndexDefinition::TYPE_UNIQUE:
				return 'UNIQUE INDEX';
			case IndexDefinition::TYPE_SPATIAL:
				return 'SPATIAL INDEX';
			case IndexDefinition::TYPE_FULLTEXT:
				return 'FULLTEXT INDEX';
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db indexes of type ' . $indexType );
		}
	}

}
