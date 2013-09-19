<?php

namespace Wikibase\Database\MySQL;

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

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName ) {
		// TODO
	}

	/**
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @return string
	 */
	public function getAddFieldSql( $tableName, FieldDefinition $field ) {
		// TODO
	}

	// TODO: add other methods

}
