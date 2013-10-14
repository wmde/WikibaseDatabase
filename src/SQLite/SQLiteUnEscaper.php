<?php

namespace Wikibase\Database\SQLite;

/**
 * UnEscaper to remove the Escaping from SQLLite escaped SQL strings
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteUnEscaper {

	/**
	 * @param string $identifier
	 * @return string
	 */
	public function getUnEscapedIdentifier( $identifier ){
		return str_replace( '""', '"', substr( $identifier, 1, -1 ) );
	}

}