<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableDefinitionReader;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MySqlTableDefinitionReader implements TableDefinitionReader {

	protected $queryInterface;

	public function __construct( QueryInterface $queryInterface ) {
		$this->queryInterface = $queryInterface;
	}

	/**
	 * @see TableDefinitionReader::readDefinition
	 *
	 * @param string $tableName
	 *
	 * @return TableDefinition
	 */
	public function readDefinition( $tableName ) {
		// TODO
	}

}
