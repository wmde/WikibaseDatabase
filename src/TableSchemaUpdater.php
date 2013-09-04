<?php

namespace Wikibase\Database;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface TableSchemaUpdater {

	/**
	 * Updates a tables schema from the old definition to the new one.
	 *
	 * @param TableDefinition $originalTable
	 * @param TableDefinition $newTable
	 *
	 * TODO: define exception
	 */
	public function updateTable( TableDefinition $originalTable, TableDefinition $newTable );

}
