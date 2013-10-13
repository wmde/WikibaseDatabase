<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;
use Wikibase\Database\TableNameFormatter;

/**
 * MySQL implementation of SchemaModificationSqlBuilder.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MySQLSchemaSqlBuilder implements SchemaModificationSqlBuilder {

	protected $escaper;
	protected $fieldSqlBuilder;
	protected $tableNameFormatter;

	public function __construct( Escaper $escaper, TableNameFormatter $tableNameFormatter ) {
		$this->escaper = $escaper;
		$this->fieldSqlBuilder = new MySQLFieldSqlBuilder( $escaper );
		$this->tableNameFormatter = $tableNameFormatter;
	}

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName ) {
		$tableName = $this->tableNameFormatter->formatTableName( $tableName );
		$fieldName = $this->escaper->getEscapedIdentifier( $fieldName );
		return "ALTER TABLE {$tableName} DROP {$fieldName}";
	}

	/**
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @return string
	 */
	public function getAddFieldSql( $tableName, FieldDefinition $field ) {
		$tableName = $this->tableNameFormatter->formatTableName( $tableName );
		return "ALTER TABLE {$tableName} ADD " . $this->fieldSqlBuilder->getFieldSQL( $field );
	}

	/**
	 * @param string $tableName
	 * @param string $indexName
	 *
	 * @return string
	 */
	public function getRemoveIndexSql( $tableName, $indexName ){
		$tableName = $this->tableNameFormatter->formatTableName( $tableName );
		$indexName = $this->escaper->getEscapedIdentifier( $indexName );
		return "DROP INDEX {$indexName} ON {$tableName}";
	}

	/**
	 * @param string $tableName
	 * @param IndexDefinition $index
	 *
	 * @return string
	 */
	public function getAddIndexSql( $tableName, IndexDefinition $index ){
		$indexSqlBuilder = new MySQLIndexSqlBuilder( $this->escaper, $this->tableNameFormatter );
		return $indexSqlBuilder->getIndexSQL( $index, $tableName );
	}

}
