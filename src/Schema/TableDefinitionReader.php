<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * Returns the TableDefinition for the specified table,
 * by reading the information from somewhere.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface TableDefinitionReader {

	/**
	 * @param string $tableName
	 *
	 * @return TableDefinition mixed
	 */
	public function readDefinition( $tableName );

}
