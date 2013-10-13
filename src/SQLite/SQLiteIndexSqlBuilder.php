<?php

namespace Wikibase\Database\SQLite;

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
class SQLiteIndexSqlBuilder extends IndexSqlBuilder {

	protected $tableNameFormatter;

	/**
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( TableNameFormatter $tableNameFormatter ) {
		$this->tableNameFormatter = $tableNameFormatter;
	}

	public function getIndexSQL( IndexDefinition $index, $tableName ){
		$sql = 'CREATE ';
		$sql .= $this->getIndexType( $index->getType() ) . ' ';
		//todo escape name once identifier escaping is implemented
		$sql .= $index->getName() . ' ';
		$sql .= 'ON ' . $this->tableNameFormatter->formatTableName( $tableName );

		$columnNames = array();
		foreach( $index->getColumns() as $columnName => $intSize ){
			$columnNames[] = $columnName;
		}

		$sql .= ' ('.implode( ',', $columnNames ).');';

		return $sql;
	}

	/**
	 * Returns the SQL field type for a given IndexDefinition type constant.
	 * Primary keys are not supported through the IndexSqlBuilder
	 *
	 * @param string $indexType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getIndexType( $indexType ) {
		switch ( $indexType ) {
			case IndexDefinition::TYPE_INDEX:
				return 'INDEX';
			case IndexDefinition::TYPE_UNIQUE:
				return 'UNIQUE INDEX';
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db indexes of type ' . $indexType );
		}
	}

}