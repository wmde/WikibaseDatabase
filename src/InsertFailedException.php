<?php

namespace Wikibase\Database;

/**
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InsertFailedException extends QueryInterfaceException {

	protected $tableName;
	protected $values;

	public function __construct( $tableName, array $values, $message = '', \Exception $previous = null ) {
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
