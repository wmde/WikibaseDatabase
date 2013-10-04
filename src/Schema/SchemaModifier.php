<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;

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

}
