<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\Escaper;
use Wikibase\Database\IdentifierEscaper;
use Wikibase\Database\ValueEscaper;

/**
 * Service for escaping values and identifiers.
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOEscaper extends PDOValueEscaper implements Escaper {

	/**
	 * @var ValueEscaper
	 */
	protected $valueEscaper;

	/**
	 * @var IdentifierEscaper
	 */
	protected $idEscaper;

	public function __construct( PDO $pdo ) {
		$this->valueEscaper = new PDOValueEscaper( $pdo );
		$this->idEscaper = new PDOIdentifierEscaper();
	}

	/**
	 * @see ValueEscaper::getEscapedValue
	 *
	 * @param mixed $value
	 *
	 * @return string The escaped value
	 */
	public function getEscapedValue( $value ) {
		return $this->valueEscaper->getEscapedValue( $value );
	}

	/**
	 * @see IdentifierEscaper::getEscapedIdentifier
	 *
	 * @param mixed $identifier
	 *
	 * @return string The escaped identifier
	 */
	public function getEscapedIdentifier( $identifier ) {
		return $this->idEscaper->getEscapedIdentifier( $identifier );
	}

}
