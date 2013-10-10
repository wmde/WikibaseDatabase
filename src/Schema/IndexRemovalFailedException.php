<?php

namespace Wikibase\Database\Schema;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IndexRemovalFailedException extends SchemaModificationException {

	protected $tableName;
	protected $indexName;

	public function __construct( $tableName, $indexName, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
		$this->indexName = $indexName;
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function getIndexName() {
		return $this->indexName;
	}

}
