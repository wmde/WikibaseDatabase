<?php

namespace Wikibase\Database\SQLite;

use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableSqlBuilder;
use Wikibase\Database\TableNameFormatter;

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
	 * @param Escaper $escaper
	 * @param TableNameFormatter $tableNameFormatter
	 * @param SQLiteFieldSqlBuilder $fieldBuilder
	 * @param SQLiteIndexSqlBuilder $indexBuilder
	 */
	public function __construct( Escaper $escaper, TableNameFormatter $tableNameFormatter,
		SQLiteFieldSqlBuilder $fieldBuilder, SQLiteIndexSqlBuilder $indexBuilder ) {

		$this->escaper = $escaper;
		$this->tableNameFormatter = $tableNameFormatter;
		$this->fieldSqlBuilder = $fieldBuilder;
		$this->indexSqlBuilder = $indexBuilder;
	}

	/**
	 * @see ExtendedAbstraction::createTable
	 * @see http://www.sqlite.org/lang_createtable.html
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @return string
	 */
	public function getCreateTableSql( TableDefinition $table ) {
		$sql = 'CREATE TABLE ' .
			$this->escaper->getEscapedIdentifier(
				$this->tableNameFormatter->formatTableName( $table->getName() )
			);

		$sql .= ' (';

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
	 * @see http://www.sqlite.org/syntaxdiagrams.html#table-constraint
	 * @return string
	 */
	protected function getPrimaryKey( $index ){
		if( $index instanceof IndexDefinition ){

			$cols = array();
			foreach( $index->getColumns() as $col ){
				$cols[] = $this->escaper->getEscapedIdentifier( $col );
			}

			return ',PRIMARY KEY (' . implode( ', ', $cols ) . ')';
		}
		return '';
	}

}
