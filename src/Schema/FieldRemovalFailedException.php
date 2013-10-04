<?php

namespace Wikibase\Database\Schema;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FieldRemovalFailedException extends SchemaModificationException {

	protected $tableName;
	protected $fieldName;

	public function __construct( $tableName, $fieldName, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
		$this->fieldName = $fieldName;
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function getFieldName() {
		return $this->fieldName;
	}

}