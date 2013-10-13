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
	protected $indexSqlBuilder;

	/**
	 * @param Escaper $fieldValueEscaper
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( Escaper $fieldValueEscaper, TableNameFormatter $tableNameFormatter ) {
		$this->escaper = $fieldValueEscaper;
		$this->tableNameFormatter = $tableNameFormatter;
		//TODO inject sqlbuilders
		$this->fieldSqlBuilder = new SQLiteFieldSqlBuilder( $this->escaper );
		$this->indexSqlBuilder = new SQLiteIndexSqlBuilder( $tableNameFormatter );
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
			$this->tableNameFormatter->formatTableName( $table->getName() ) . ' (';

		$fields = array();

		foreach ( $table->getFields() as $field ) {
			$fields[] = $this->fieldSqlBuilder->getFieldSQL( $field );
		}

		$sql .= implode( ', ', $fields );

		$primaryKey = $this->getPrimaryIndex( $table->getIndexes() );
		if( !is_null( $primaryKey ) ){
			$table = $table->mutateIndexAway( $primaryKey->getName() );
			if( !strstr( $sql, 'PRIMARY KEY' ) ){
				$sql .= $this->getPrimaryKey( $primaryKey );
			}
		}

		$sql .= ');';

		foreach ( $table->getIndexes() as $index ){
			$sql .= PHP_EOL . $this->indexSqlBuilder->getIndexSQL( $index, $table->getName() );
		}

		return $sql;
	}

	/**
	 * @param IndexDefinition[] $indexes
	 * @return IndexDefinition|null
	 */
	protected function getPrimaryIndex( $indexes ){
		foreach( $indexes as $index ){
			if( $index->getType() === IndexDefinition::TYPE_PRIMARY ){
				return $index;
			}
		}
		return null;
	}

	/**
	 * @param IndexDefinition $index
	 * @return string
	 */
	protected function getPrimaryKey( $index ){
		if( $index instanceof IndexDefinition ){

			$cols = array();
			foreach( $index->getColumns() as $col => $length ){
				$cols[] = $col;
			}

			return ',PRIMARY KEY (' . implode( ', ', $cols ) . ')';
		}
		return '';
	}

}
