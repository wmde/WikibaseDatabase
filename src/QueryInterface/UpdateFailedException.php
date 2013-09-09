<?php

namespace Wikibase\Database\QueryInterface;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UpdateFailedException extends QueryInterfaceException {

	protected $tableName;
	protected $values;
	protected $conditions;

	public function __construct( $tableName, array $values, array $conditions, $message = '', \Exception $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
		$this->conditions = $conditions;
		$this->values = $values;
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
	public function getValues() {
		return $this->values;
	}

}