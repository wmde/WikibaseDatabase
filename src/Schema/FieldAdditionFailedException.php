<?php

namespace Wikibase\Database\Schema;

use Wikibase\Database\Schema\Definitions\FieldDefinition;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FieldAdditionFailedException extends SchemaModificationException {

	protected $tableName;
	protected $field;

	public function __construct( $tableName, FieldDefinition $field, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
		$this->field = $field;
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function getField() {
		return $this->field;
	}

}