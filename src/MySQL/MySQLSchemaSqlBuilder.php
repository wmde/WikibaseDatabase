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
	 * @see http://dev.mysql.com/doc/refman/5.7/en/alter-table.html
	 *
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName ) {
		$tableName = $this->getPreparedTableName( $tableName );
		$fieldName = $this->escaper->getEscapedIdentifier( $fieldName );
		return "ALTER TABLE {$tableName} DROP {$fieldName}";
	}

	protected function getPreparedTableName( $tableName ) {
		return $this->escaper->getEscapedIdentifier(
			$this->tableNameFormatter->formatTableName( $tableName )
		);
	}

	/**
	 * @see http://dev.mysql.com/doc/refman/5.7/en/alter-table.html
	 *
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @return string
	 */
	public function getAddFieldSql( $tableName, FieldDefinition $field ) {
		$tableName = $this->getPreparedTableName( $tableName );
		return "ALTER TABLE {$tableName} ADD " . $this->fieldSqlBuilder->getFieldSQL( $field );
	}

	/**
	 * @see http://dev.mysql.com/doc/refman/5.7/en/drop-index.html
	 *
	 * @param string $tableName
	 * @param string $indexName
	 *
	 * @return string
	 */
	public function getRemoveIndexSql( $tableName, $indexName ){
		$tableName = $this->getPreparedTableName( $tableName );
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
