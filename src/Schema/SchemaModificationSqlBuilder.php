<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;

/**
 * The SQL returned by the methods in this interface may contain multiple statements.
 * The SQL may also consist out of multiple lines. One statement per line.
 * Multiple statements on one line is not allowed, and neither is spreading a
 * statement over multiple lines.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface SchemaModificationSqlBuilder {

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @return string
	 */
	public function getRemoveFieldSql( $tableName, $fieldName );

	/**
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @return string
	 */
	public function getAddFieldSql( $tableName, FieldDefinition $field );

	/**
	 * @param string $tableName
	 * @param string $indexName
	 *
	 * @return string
	 */
	public function getRemoveIndexSql( $tableName, $indexName );

	/**
	 * @param string $tableName
	 * @param IndexDefinition $index
	 *
	 * @return string
	 */
	public function getAddIndexSql( $tableName, IndexDefinition $index );

}
