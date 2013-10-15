<?php

namespace Wikibase\Database\SQLite;

use Exception;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;
use Wikibase\Database\TableNameFormatter;

/**
 * SQLite implementation of SchemaModificationSqlBuilder.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class SQLiteSchemaSqlBuilder implements SchemaModificationSqlBuilder {

	protected $escaper;
	protected $fieldSqlBuilder;
	protected $tableNameFormatter;
	protected $tableDefinitionReader;
	protected $tableSqlBuilder;

	public function __construct( Escaper $escaper, TableNameFormatter $tableNameFormatter, SQLiteTableDefinitionReader $definitionReader ) {
		$this->escaper = $escaper;
		$this->fieldSqlBuilder = new SQLiteFieldSqlBuilder( $escaper );
		$this->tableNameFormatter = $tableNameFormatter;
		$this->tableDefinitionReader = $definitionReader;
		//todo inject SQLiteTableSqlBuilder to make testing easier?
		$this->tableSqlBuilder = new SQLiteTableSqlBuilder(
			$escaper,
			$tableNameFormatter,
			new SQLiteFieldSqlBuilder( $escaper ),
			new SQLiteIndexSqlBuilder( $escaper, $tableNameFormatter )
		);
	}

	/**
	 * This returns sql to rename the table to a temporary name,
	 * create a new table with the new definition (without the field we are removing)
	 * copy all of the data across and drop the temporary table.
	 * The returned string consists of these 4 separate queries divided by the PHP_EOL character
	 *
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @throws Exception
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName ) {
		$definition = $this->tableDefinitionReader->readDefinition( $tableName );

		$tmpTableName = $tableName . '_tmp';
		$sql = $this->getRenameTableSql( $tableName, $tmpTableName ) . PHP_EOL;

		/** @var TableDefinition $definition */
		$definition = $definition->mutateFieldAway( $fieldName );

		$sql .= $this->tableSqlBuilder->getCreateTableSql( $definition ) . PHP_EOL;
		$sql .= $this->getContentsCopySql( $tmpTableName, $tableName, $definition->getFields() ) . PHP_EOL;
		$sql .= $this->getDropTableSql( $tmpTableName );

		return $sql;
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#alter-table-stmt
	 */
	protected function getRenameTableSql( $fromTable, $toTable ){
		$fromTable = $this->tableNameFormatter->formatTableName( $fromTable );
		$toTable = $this->tableNameFormatter->formatTableName( $toTable );
		return "ALTER TABLE {$fromTable} RENAME TO {$toTable};";
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#insert-stmt
	 */
	protected function getContentsCopySql( $fromTable, $toTable, $fields ){
		$fromTable = $this->tableNameFormatter->formatTableName( $fromTable );
		$toTable = $this->tableNameFormatter->formatTableName( $toTable );
		$fieldsSql = $this->getFieldsSql( $fields );
		return "INSERT INTO {$toTable}({$fieldsSql}) SELECT {$fieldsSql} FROM {$fromTable};";
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#drop-table-stmt
	 */
	protected function getDropTableSql( $tableName ){
		$tableName = $this->tableNameFormatter->formatTableName( $tableName );
		return "DROP TABLE {$tableName};";
	}

	/**
	 * @param FieldDefinition[] $fields
	 * @return string
	 */
	private function getFieldsSql( $fields ){
		$fieldNames = array();
		foreach( $fields as $field ){
			$fieldNames[] = $this->escaper->getEscapedIdentifier( $field->getName() );
		}
		return implode( ', ', $fieldNames );
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#alter-table-stmt
	 *
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @return string
	 */
	public function getAddFieldSql( $tableName, FieldDefinition $field ) {
		$tableName = $this->tableNameFormatter->formatTableName( $tableName );
		return "ALTER TABLE {$tableName} ADD COLUMN " . $this->fieldSqlBuilder->getFieldSQL( $field );
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#drop-index-stmt
	 *
	 * @param string $tableName Ignored by this method
	 * @param string $indexName
	 *
	 * @return string
	 */
	public function getRemoveIndexSql( $tableName, $indexName ){
		$indexName = $this->escaper->getEscapedIdentifier( $indexName );
		return "DROP INDEX IF EXISTS {$indexName}";
	}

	/**
	 * @param string $tableName
	 * @param IndexDefinition $index
	 *
	 * @return string
	 */
	public function getAddIndexSql( $tableName, IndexDefinition $index ){
		$indexSqlBuilder = new SQLiteIndexSqlBuilder( $this->escaper, $this->tableNameFormatter );
		return $indexSqlBuilder->getIndexSQL( $index, $tableName );
	}

}
