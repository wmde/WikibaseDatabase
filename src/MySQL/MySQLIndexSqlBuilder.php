<?php

namespace Wikibase\Database\MySQL;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\IndexSqlBuilder;
use Wikibase\Database\TableNameFormatter;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MySQLIndexSqlBuilder extends IndexSqlBuilder {

	protected $escaper;
	protected $tableNameFormatter;

	/**
	 * @var IndexDefinition
	 */
	protected $index;

	/**
	 * @param Escaper $escaper
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( Escaper $escaper, TableNameFormatter $tableNameFormatter ) {
		// The only TableNameFormatter implementation I could find is based on the MediaWiki DB class. Is that OK?
		$this->escaper = $escaper;
		$this->tableNameFormatter = $tableNameFormatter;
	}

	/**
	 * @see http://dev.mysql.com/doc/refman/5.7/en/create-index.html
	 */
	public function getIndexSQL( IndexDefinition $index, $tableName ) {
		$this->index = $index;

		$sql = $this->getCreateIndexTypeName();
		$sql .= $this->getOnTableSQL( $tableName );
		$sql .= $this->getColumnsSQL();

		return $sql;
	}

	protected function getCreateIndexTypeName() {
		$sql = 'CREATE ';
		$sql .= $this->getIndexType();

		if ( $this->index->getType() !== IndexDefinition::TYPE_PRIMARY ) {
			$sql .= ' ' . $this->escaper->getEscapedIdentifier( $this->index->getName() );
		}

		return $sql;
	}

	protected function getOnTableSQL( $tableName ) {
		return ' ON ' . $this->getPreparedTableName( $tableName );
	}

	protected function getPreparedTableName( $tableName ) {
		return $this->escaper->getEscapedIdentifier(
			$this->tableNameFormatter->formatTableName( $tableName )
		);
	}

	protected function getColumnsSQL() {
		return
			' (' .
				implode(
					',',
					$this->getEscapedColumnNames()
				).
			')';
	}

	protected function getEscapedColumnNames() {
		$columnNames = array();

		foreach( $this->index->getColumns() as $columnName => $intSize ) {
			$columnNames[] =  $this->escaper->getEscapedIdentifier( $columnName );
		}

		return $columnNames;
	}

	/**
	 * Returns the MySQL field type for a given IndexDefinition type constant.
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getIndexType() {
		switch ( $this->index->getType() ) {
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
				throw new RuntimeException(
					__CLASS__ . ' does not support db indexes of type ' . $this->index->getType()
				);
		}
	}

}