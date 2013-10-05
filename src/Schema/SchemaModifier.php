<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface SchemaModifier {

	/**
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @throws FieldRemovalFailedException
	 */
	public function removeField( $tableName, $fieldName );

	/**
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @throws FieldAdditionFailedException
	 */
	public function addField( $tableName, FieldDefinition $field );

	/**
	 * @param string $tableName
	 * @param string $indexName
	 *
	 * TODO: document throws
	 */
	public function removeIndex( $tableName, $indexName );

	/**
	 * @param string $tableName
	 * @param IndexDefinition $index
	 *
	 * TODO: document throws
	 */
	public function addIndex( $tableName, IndexDefinition $index );

}
