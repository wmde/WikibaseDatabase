<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
abstract class FieldSqlBuilder {

	/**
	 * @since 0.1
	 *
	 * @param FieldDefinition $field
	 *
	 * @return string The SQL for creating the field
	 */
	public abstract function getFieldSQL( FieldDefinition $field );

}