<?php

namespace Wikibase\Database\QueryInterface;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SelectFailedException extends QueryInterfaceException {

	protected $tableName;
	protected $fields;
	protected $conditions;

	public function __construct( $tableName, array $fields, array $conditions, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
		$this->conditions = $conditions;
		$this->fields = $fields;
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * @return array
	 */
	public function getConditions() {
		return $this->conditions;
	}

	/**
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

}