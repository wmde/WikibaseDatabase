<?php

namespace Wikibase\Database\SQLite;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\IndexSqlBuilder;
use Wikibase\Database\TableNameFormatter;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteIndexSqlBuilder extends IndexSqlBuilder {

	protected $escaper;
	protected $tableNameFormatter;

	/**
	 * @param Escaper $escaper
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( Escaper $escaper, TableNameFormatter $tableNameFormatter ) {
		$this->escaper = $escaper;
		$this->tableNameFormatter = $tableNameFormatter;
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#create-index-stmt
	 */
	public function getIndexSQL( IndexDefinition $index, $tableName ){
		$sql = 'CREATE ';
		$sql .= $this->getIndexType( $index->getType() ) . ' ';
		$sql .= $this->escaper->getEscapedIdentifier( $index->getName() ) . ' ';

		$sql .= 'ON ' . $this->escaper->getEscapedIdentifier(
				$this->tableNameFormatter->formatTableName( $tableName )
			);

		$columnNames = array();
		foreach( $index->getColumns() as $columnName => $intSize ){
			$columnNames[] = $this->escaper->getEscapedIdentifier( $columnName );
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