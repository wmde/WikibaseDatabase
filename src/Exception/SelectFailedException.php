<?php

namespace Wikibase\Database\Exception;

use Exception;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SelectFailedException extends QueryInterfaceException {

	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * @var array
	 */
	private $fields;

	/**
	 * @var array
	 */
	private $conditions;

	/**
	 * @param string $tableName
	 * @param array $fields
	 * @param array $conditions
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct(
		$tableName,
		array $fields,
		array $conditions,
		$message = '',
		Exception $previous = null
	) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
		$this->fields = $fields;
		$this->conditions = $conditions;
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
	public function getFields() {
		return $this->fields;
	}

	/**
	 * @return array
	 */
	public function getConditions() {
		return $this->conditions;
	}

}
