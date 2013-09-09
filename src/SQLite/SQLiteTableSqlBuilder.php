<?php

namespace Wikibase\Database\SQLite;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\IndexDefinition;
use Wikibase\Database\TableDefinition;
use Wikibase\Database\TableNameFormatter;
use Wikibase\Database\TableSqlBuilder;

/**
 * SQLite implementation of TableSqlBuilder.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class SQLiteTableSqlBuilder extends TableSqlBuilder {

	protected $escaper;
	protected $tablePrefix;
	protected $tableNameFormatter;

	/**
	 * @param string $tablePrefix
	 * @param Escaper $fieldValueEscaper
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( $tablePrefix, Escaper $fieldValueEscaper, TableNameFormatter $tableNameFormatter ) {
		$this->tablePrefix = $tablePrefix;
		$this->escaper = $fieldValueEscaper;
		$this->tableNameFormatter = $tableNameFormatter;
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
		// TODO: get rid of global (DatabaseBase currently provides no access to its mTablePrefix field)
		$sql = 'CREATE TABLE ' .
			$this->tableNameFormatter->formatTableName( $this->tablePrefix . $table->getName() ) . ' (';

		$fields = array();

		foreach ( $table->getFields() as $field ) {
			$fields[] = $field->getName() . ' ' . $this->getFieldSQL( $field );
		}

		$sql .= implode( ', ', $fields );

		// TODO: table options
		$sql .= ');';

		foreach ( $table->getIndexes() as $index ){
			$sql .= $this->getIndexSQL( $index, $table );
		}

		return $sql;
	}

	/**
	 * @since 0.1
	 *
	 * @param FieldDefinition $field
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldSQL( FieldDefinition $field ) {
		$sql = $this->getFieldType( $field->getType() );

		$sql .= $this->getDefault( $field->getDefault() );

		$sql .= $this->getNull( $field->allowsNull() );

		return $sql;
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
		$sql .= 'ON '.$this->tablePrefix . $table->getName();

		$columnNames = array();
		foreach( $index->getColumns() as $columnName => $intSize ){
			$columnNames[] = $columnName;
		}

		$sql .= ' ('.implode( ',', $columnNames ).');';

		return $sql;
	}

	protected function getDefault( $default ) {
		if ( $default !== null ) {
			return ' DEFAULT ' . $this->escaper->getEscapedValue( $default );
		}

		return '';
	}

	protected function getNull( $allowsNull ) {
		return $allowsNull ? ' NULL' : ' NOT NULL';
	}

	/**
	 * Returns the MySQL field type for a given FieldDefinition type constant.
	 *
	 * @param string $fieldType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldType( $fieldType ) {
		switch ( $fieldType ) {
			case FieldDefinition::TYPE_INTEGER:
				return 'INT';
			case FieldDefinition::TYPE_FLOAT:
				return 'FLOAT';
			case FieldDefinition::TYPE_TEXT:
				return 'BLOB';
			case FieldDefinition::TYPE_BOOLEAN:
				return 'TINYINT';
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db fields of type ' . $fieldType );
		}
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
