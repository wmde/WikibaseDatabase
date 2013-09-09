<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface TableSchemaUpdater {

	/**
	 * Updates a tables schema from the old definition to the new one.
	 *
	 * @param TableDefinition $currentTable
	 * @param TableDefinition $newTable
	 *
	 * @throws SchemaUpdateFailedException
	 */
	public function updateTable( TableDefinition $currentTable, TableDefinition $newTable );

}
