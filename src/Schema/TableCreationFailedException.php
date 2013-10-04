<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\QueryInterface\QueryInterfaceException;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableCreationFailedException extends SchemaModificationException {

	protected $table;

	public function __construct( TableDefinition $table, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->table = $table;
	}

	public function getTable() {
		return $this->table;
	}

}