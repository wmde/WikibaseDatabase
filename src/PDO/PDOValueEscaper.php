<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\ValueEscaper;

/**
 * Service for escaping values and identifiers.
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOValueEscaper implements ValueEscaper {

	protected $pdo;

	public function __construct( PDO $pdo ) {
		$this->pdo = $pdo;
	}

	/**
	 * @see Escaper::getEscapedValue
	 *
	 * @param mixed $value
	 *
	 * @return string The escaped value
	 */
	public function getEscapedValue( $value ) {
		return $this->pdo->quote( $value );
	}

}
