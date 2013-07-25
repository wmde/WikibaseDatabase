<?php

namespace Wikibase\Database;

/**
 * Base class acting as interface for classes that escape values so they
 * are suitable for injection in an SQL string.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface Escaper {

	/**
	 * @param mixed $value
	 *
	 * @return string The escaped value
	 */
	public function getEscapedValue( $value );

}
