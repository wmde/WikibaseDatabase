<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\IndexDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IndexAdditionFailedException extends SchemaModificationException {

	protected $tableName;
	protected $index;

	public function __construct( $tableName, IndexDefinition $index, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
		$this->index = $index;
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function getIndex() {
		return $this->index;
	}

}
