<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;

/**
 * MySQL implementation of SchemaModificationSqlBuilder.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MySQLSchemaSqlBuilder implements SchemaModificationSqlBuilder {

	protected $fieldSqlBuilder;

	/**
	 * @param Escaper $fieldValueEscaper
	 */
	public function __construct( Escaper $fieldValueEscaper ) {
		$this->fieldSqlBuilder = new MySQLFieldSqlBuilder( $fieldValueEscaper );
	}

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName ) {
		return "ALTER TABLE {$tableName} DROP {$fieldName}";
	}

	/**
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @return string
	 */
	public function getAddFieldSql( $tableName, FieldDefinition $field ) {
		return "ALTER TABLE {$tableName} ADD " . $this->fieldSqlBuilder->getFieldSQL( $field );
	}

	// TODO: add other methods

}
