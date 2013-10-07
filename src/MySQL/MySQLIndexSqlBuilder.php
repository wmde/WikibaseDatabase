<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\IndexSqlBuilder;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class MySQLIndexSqlBuilder extends IndexSqlBuilder {

	public function getIndexSQL( IndexDefinition $index, TableDefinition $table ){
		//TODO take logic from MySQLTableSqlBuilder
	}

}