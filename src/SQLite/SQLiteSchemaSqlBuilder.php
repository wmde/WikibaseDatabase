<?php

namespace Wikibase\Database\SQLite;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;

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

	/**
	 * @param Escaper $fieldValueEscaper
	 */
	public function __construct( Escaper $fieldValueEscaper ) {
		$this->fieldSqlBuilder = new SQLiteFieldSqlBuilder( $fieldValueEscaper );
	}

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName ) {
		// TODO this will need to create a new table with the new scheme and copy all data across
	}

	/**
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @return string
	 */
	public function getAddFieldSql( $tableName, FieldDefinition $field ) {
		//TODO add unittests
		return "ALTER TABLE {$tableName} ADD COLUMN " . $this->fieldSqlBuilder->getFieldSQL( $field );
	}

	// TODO: add other methods

}
