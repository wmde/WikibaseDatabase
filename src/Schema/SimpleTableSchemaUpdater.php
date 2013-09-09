<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SimpleTableSchemaUpdater implements TableSchemaUpdater {

	protected $schemaModifier;

	public function __construct( SchemaModifier $schemaModifier ) {
		$this->schemaModifier = $schemaModifier;
	}

	/**
	 * @see TableSchemaUpdater::updateTable
	 *
	 * @param TableDefinition $currentTable
	 * @param TableDefinition $newTable
	 *
	 * @throws SchemaUpdateFailedException
	 */
	public function updateTable( TableDefinition $currentTable, TableDefinition $newTable ) {
		$this->removeRemovedFields( $currentTable, $newTable );
		// TODO
	}

	protected function removeRemovedFields( TableDefinition $currentTable, TableDefinition $newTable ) {
		$removedFields = array_diff_key( $currentTable->getFields(), $newTable->getFields() );
		// TODO
	}

}
