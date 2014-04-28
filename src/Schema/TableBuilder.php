<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface TableBuilder {

	/**
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @throws TableCreationFailedException
	 */
	public function createTable( TableDefinition $table );

	/**
	 * If the table does not exist, an exception is thrown.
	 *
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * @throws TableDeletionFailedException
	 */
	public function dropTable( $tableName );

	/**
	 * @since 0.1
	 *
	 * @param string $tableName
	 *
	 * @return boolean
	 * @throws SchemaModificationException
	 */
	public function tableExists( $tableName );

}
