<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * The SQL returned by the methods in this interface may contain multiple statements.
 * The SQL may also consist out of multiple lines. One statement per line.
 * Multiple statements on one line is not allowed, and neither is spreading a
 * statement over multiple lines.
 *
 * @since 0.1
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