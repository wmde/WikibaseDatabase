<?php

namespace Wikibase\Database;

/**
 * Object that can create a table in a database given a table definition.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableBuilder {

	/**
	 * @since 0.1
	 *
	 * @var QueryInterface
	 */
	private $db;

	/**
	 * @since 0.1
	 *
	 * @var MessageReporter|null
	 */
	private $messageReporter;

	/**
	 * @since 0.1
	 *
	 * @param QueryInterface $queryInterface
	 * @param MessageReporter|null $messageReporter
	 */
	public function __construct( QueryInterface $queryInterface, MessageReporter $messageReporter = null ) {
		$this->db = $queryInterface;
		$this->messageReporter = $messageReporter;
	}

	/**
	 * @since 0.1
	 *
	 * @param string $message
	 */
	private function report( $message ) {
		if ( $this->messageReporter !== null ) {
			$this->messageReporter->reportMessage( $message );
		}
	}

	/**
	 * Creates a table if it does not exist yet.
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 */
	public function createTable( TableDefinition $table ) {
		if ( $this->db->tableExists( $table->getName() ) ) {
			$this->report( 'Table "' . $table->getName() . '" exists already, skipping.' );
			return true;
		}

		$this->report( 'Table "' . $table->getName() . '" not found, creating.' );

		$this->db->createTable( $table );
	}

}