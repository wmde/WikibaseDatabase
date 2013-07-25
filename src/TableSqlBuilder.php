<?php

namespace Wikibase\Database;

use DatabaseBase;
use Wikibase\Database\TableDefinition;

/**
 * Base database abstraction class to put stuff into that is not present
 * in the MW core db abstraction layer.
 *
 * Like to core class DatabaseBase, each deriving class provides support
 * for a specific type of database.
 *
 * Everything implemented in these classes could go into DatabaseBase and
 * deriving classes, though this might take quite some time, hence implementation
 * is first done here. If you feel like taking core CR crap and waiting a few
 * months, by all means try to get the functionality into core.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class TableSqlBuilder {

	/**
	 * Create the provided table.
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @return string The SQL for creating the table
	 */
	public abstract function getCreateTableSql( TableDefinition $table );

}