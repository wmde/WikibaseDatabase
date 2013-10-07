<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\IndexDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
abstract class IndexSqlBuilder {

	/**
	 * @since 0.1
	 *
	 * @param IndexDefinition $index
	 * @param string $tableNameName
	 *
	 * @return string The SQL for creating the index
	 */
	public abstract function getIndexSQL( IndexDefinition $index, $tableNameName );

}