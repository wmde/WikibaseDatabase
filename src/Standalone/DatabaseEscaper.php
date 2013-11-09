<?php

namespace Wikibase\Database\Standalone;

use DatabaseBase;
use Wikibase\Database\Escaper;

/**
 * Service for escaping values and identifiers.
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DatabaseEscaper implements Escaper {

	protected $dbConnection;

	public function __construct( DatabaseBase $dbConnection ) {
		$this->dbConnection = $dbConnection;
	}

	/**
	 * @see Escaper::getEscapedValue
	 *
	 * @param mixed $value
	 *
	 * @return string The escaped value
	 */
	public function getEscapedValue( $value ) {
		return $this->dbConnection->addQuotes( $value );
	}

	/**
	 * @see Escaper::getEscapedIdentifier
	 *
	 * @param mixed $identifier
	 *
	 * @return string The escaped identifier
	 */
	public function getEscapedIdentifier( $identifier ) {
		return $this->dbConnection->addIdentifierQuotes( $identifier );
	}

}
