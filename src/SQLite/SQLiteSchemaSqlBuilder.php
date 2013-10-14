<?php

namespace Wikibase\Database\SQLite;

use Exception;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
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
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @throws Exception
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName ) {
		$definition = $this->tableDefinitionReader->readDefinition( $tableName );
		$tableName = $this->tableNameFormatter->formatTableName( $tableName );
		$tmpTableName = $this->tableNameFormatter->formatTableName( $tableName . '_tmp' );
		$sql = "ALTER TABLE {$tableName} RENAME TO {$tmpTableName};" . PHP_EOL;

		$definition = $definition->mutateFieldAway( $fieldName );
		$sql .= $this->tableSqlBuilder->getCreateTableSql( $definition ) . PHP_EOL;

		$fieldsSql = $this->getFieldsSql( $definition->getFields() );
		$sql .= "INSERT INTO {$tableName}({$fieldsSql}) SELECT {$fieldsSql} FROM {$tmpTableName};" . PHP_EOL;
		$sql .= "DROP TABLE {$tmpTableName};";

		return $sql;
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
	 * @param string $tableName
	 * @param string $indexName
	 *
	 * @return string
	 */
	public function getRemoveIndexSql( $tableName, $indexName ){
		$tableName = $this->tableNameFormatter->formatTableName( $tableName );
		return "DROP INDEX IF EXISTS {$tableName}.{$indexName}";
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
