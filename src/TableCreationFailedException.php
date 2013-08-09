<?php

namespace Wikibase\Database;

/**
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableCreationFailedException extends QueryInterfaceException {

	protected $table;

	public function __construct( TableDefinition $table, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->table = $table;
	}

	public function getTable() {
		return $this->table;
	}

}