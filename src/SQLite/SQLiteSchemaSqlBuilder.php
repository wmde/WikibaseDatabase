<?php

namespace Wikibase\Database\SQLite;

use Exception;
use Wikibase\Database\Escaper;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;
use Wikibase\Database\Schema\TableDefinitionReader;
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

	protected $fieldSqlBuilder;
	protected $tableNameFormatter;

	public function __construct( Escaper $fieldValueEscaper, TableNameFormatter $tableNameFormatter, TableDefinitionReader $definitionReader ) {
		$this->fieldSqlBuilder = new SQLiteFieldSqlBuilder( $fieldValueEscaper );
		$this->tableNameFormatter = $tableNameFormatter;
		$this->tableDefinitionReader = $definitionReader;
		$this->tableSqlBuilder = new SQLiteTableSqlBuilder( $fieldValueEscaper, $tableNameFormatter );
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
		$sql = "ALTER TABLE {$tableName} RENAME TO {$tmpTableName};";

		$fields = $this->removeFieldFromFields( $definition->getFields(), $fieldName );
		$definition = $definition->mutateFields( $fields );
		$sql .= $this->tableSqlBuilder->getCreateTableSql( $definition );

		$fieldsSql = $this->getFieldsSql( $fields );
		$sql .= "INSERT INTO {$tableName}({$fieldsSql}) SELECT {$fieldsSql} FROM {$tmpTableName};";
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
			$fieldNames[] = $field->getName();
		}
		return implode( ', ', $fieldNames );
	}

	/**
	 * @param FieldDefinition[] $fields
	 * @param string $fieldName field to remove
	 * @return FieldDefinition[]
	 * @todo tableDefinition->removeFieldWithName( $name )
	 */
	private function removeFieldFromFields( $fields, $fieldName ){
		foreach( $fields as $key => $field ){
			if( $field->getName() == $fieldName ){
				unset ( $fields[$key] );
			}
		}
		return $fields;
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

	// TODO: add other methods

}
