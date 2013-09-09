<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\MessageReporter;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * Decorator for TableBuilder objects.
 *
 * Add progress reporting for table creation and deletion via the injected MessageReporter.
 * Also checks if a table exists before creating or dropping it, and if so, skips the operation.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReportingTableBuilder implements TableBuilder {

	protected $tableBuilder;
	protected $messageReporter;

	public function __construct( TableBuilder $tableBuilder, MessageReporter $messageReporter = null ) {
		$this->tableBuilder = $tableBuilder;
		$this->messageReporter = $messageReporter;
	}

	/**
	 * Creates a table if it does not exist yet.
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @throws TableCreationFailedException
	 */
	public function createTable( TableDefinition $table ) {
		if ( $this->tableBuilder->tableExists( $table->getName() ) ) {
			$this->report( 'Table "' . $table->getName() . '" exists already, skipping.' );
			return;
		}

		$this->report( 'Table "' . $table->getName() . '" not found, creating.' );

		$this->tableBuilder->createTable( $table );

		$this->report( 'Table "' . $table->getName() . '" created.' );
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
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * TODO: document throws
	 */
	public function dropTable( $tableName ) {
		if ( $this->tableBuilder->tableExists( $tableName ) ) {
			$this->report( 'Table "' . $tableName . '" found, dropping.' );
			$this->tableBuilder->dropTable( $tableName );
			$this->report( 'Table "' . $tableName . '" dropped.' );
		}
		else {
			$this->report( 'Table "' . $tableName . '" does not exist, so no need to drop it.' );
		}
	}

	/**
	 * Returns if the table exists in the database.
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * @return boolean
	 */
	public function tableExists( $tableName ) {
		return $this->tableBuilder->tableExists( $tableName );
	}

}
