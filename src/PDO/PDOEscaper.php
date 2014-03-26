<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\Escaper;

/**
 * Service for escaping values and identifiers.
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOEscaper implements Escaper {

	public function __construct( PDO $pdo ) {
		$this->pdo = $pdo;
	}

	/**
	 * @see ValueEscaper::getEscapedValue
	 *
	 * @param mixed $value
	 *
	 * @return string The escaped value
	 */
	public function getEscapedValue( $value ) {
		return $this->pdo->quote( $value );
	}

	/**
	 * @see IdentifierEscaper::getEscapedIdentifier
	 *
	 * @param mixed $identifier
	 *
	 * @return string The escaped identifier
	 */
	public function getEscapedIdentifier( $identifier ) {
		return '`' . str_replace( '`', '``', $identifier ) . '`';
	}

}
