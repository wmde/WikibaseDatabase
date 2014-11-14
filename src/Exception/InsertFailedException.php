<?php

namespace Wikibase\Database\Exception;

use Exception;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InsertFailedException extends QueryInterfaceException {

	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * @var array
	 */
	private $values;

	/**
	 * @param string $tableName
	 * @param array $values
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct(
		$tableName,
		array $values,
		$message = '',
		Exception $previous = null
	) {
		parent::__construct( $message, 0, $previous );

		$this->tableName = $tableName;
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
	public function getValues() {
		return $this->values;
	}

}
