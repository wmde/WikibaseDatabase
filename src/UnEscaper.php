<?php

namespace Wikibase\Database;

/**
 * Base class acting as interface for classes that un-escape values so they
 * can be taken from SQL string and put back in objects
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
interface UnEscaper {

	/**
	 * @param mixed $identifier
	 *
	 * @return string The unescaped identifier
	 */
	public function getUnEscapedIdentifier( $identifier );

}
