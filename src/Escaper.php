<?php

namespace Wikibase\Database;

/**
 * Base class acting as interface for classes that escape values so they
 * are suitable for injection in an SQL string.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
interface Escaper {

	/**
	 * @param mixed $value
	 *
	 * @return string The escaped value
	 */
	public function getEscapedValue( $value );

	/**
	 * @param mixed $identifier
	 *
	 * @return string The escaped identifier
	 */
	public function getEscapedIdentifier( $identifier );

}
