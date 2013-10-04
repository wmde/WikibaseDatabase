<?php

namespace Wikibase\Database\SQLite;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\TableNameFormatter;
use Wikibase\Database\Schema\TableSqlBuilder;

/**
 * SQLite implementation of TableSqlBuilder.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class SQLiteTableSqlBuilder extends TableSqlBuilder {

	protected $escaper;
	protected $tableNameFormatter;
	protected $fieldSqlBuilder;

	/**
	 * @param Escaper $fieldValueEscaper
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( Escaper $fieldValueEscaper, TableNameFormatter $tableNameFormatter ) {
		$this->escaper = $fieldValueEscaper;
		$this->tableNameFormatter = $tableNameFormatter;
		$this->fieldSqlBuilder = new SQLiteFieldSqlBuilder( $this->escaper );
	}

	/**
	 * @see ExtendedAbstraction::createTable
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @return string
	 */
	public function getCreateTableSql( TableDefinition $table ) {
		$sql = 'CREATE TABLE ' .
			$this->formatTableName( $table->getName() ) . ' (';

		$fields = array();

		foreach ( $table->getFields() as $field ) {
			$fields[] = $this->fieldSqlBuilder->getFieldSQL( $field );
		}

		$sql .= implode( ', ', $fields );

		$sql .= ');';

		foreach ( $table->getIndexes() as $index ){
			$sql .= $this->getIndexSQL( $index, $table );
		}

		return $sql;
	}

	protected function formatTableName( $name ) {
		return $this->tableNameFormatter->formatTableName( $name );
	}

	/**
	 * @since 0.1
	 *
	 * @param IndexDefinition $index
	 * @param TableDefinition $table
	 *
	 * @return string
	 */
	protected function getIndexSQL( IndexDefinition $index, TableDefinition $table ) {
		$sql = 'CREATE ';
		$sql .= $this->getIndexType( $index->getType() ) . ' ';
		$sql .= $index->getName() . ' ';
		$sql .= 'ON ' . $this->formatTableName( $table->getName() );

		$columnNames = array();
		foreach( $index->getColumns() as $columnName => $intSize ){
			$columnNames[] = $columnName;
		}

		$sql .= ' ('.implode( ',', $columnNames ).');';

		return $sql;
	}

	/**
	 * Returns the SQL field type for a given IndexDefinition type constant.
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
