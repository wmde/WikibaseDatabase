<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\FieldDefinition;
use Wikibase\Database\QueryInterface;
use Wikibase\Database\TableDefinition;
use Wikibase\Database\TableDefinitionReader;

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
