<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\Escaper;
use Wikibase\Database\IdentifierEscaper;

/**
 * Service for escaping identifiers.
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOIdentifierEscaper implements IdentifierEscaper {

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
