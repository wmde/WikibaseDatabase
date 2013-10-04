<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableSchemaUpdateException extends \Exception {

	protected $currentTable;
	protected $newTable;

	public function __construct( TableDefinition $currentTable, TableDefinition $newTable, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->currentTable = $currentTable;
		$this->newTable = $newTable;
	}

	public function getCurrentTable() {
		return $this->currentTable;
	}

	public function getNewTable() {
		return $this->newTable;
	}

}
