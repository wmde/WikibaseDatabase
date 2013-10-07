<?php

namespace Wikibase\Database\MySQL;

use RuntimeException;
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

	/**
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( TableNameFormatter $tableNameFormatter ) {
		$this->tableNameFormatter = $tableNameFormatter;
	}

	public function getIndexSQL( IndexDefinition $index, $tableName ){
		$sql = 'CREATE ';
		$sql .= $this->getIndexType( $index->getType() );

		if( $index->getType() !== IndexDefinition::TYPE_PRIMARY ){
			$sql .= ' `'.$index->getName().'`';//todo escape index name?
		}

		$sql .= ' ON ' . $this->tableNameFormatter->formatTableName( $tableName );

		$columnNames = array();
		foreach( $index->getColumns() as $columnName => $intSize ){
			$columnNames[] = $columnName;
		}

		$sql .= ' (`'.implode( '`,`', $columnNames ).'`)';

		return $sql;
	}

	/**
	 * Returns the MySQL field type for a given IndexDefinition type constant.
	 *
	 * @param string $indexType
	 *
	 * @return string
	 * @throws RuntimeException
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