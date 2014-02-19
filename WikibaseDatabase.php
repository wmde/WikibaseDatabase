<?php

/**
 * Entry point of the Wikibase Database component.
 *
 * @codeCoverageIgnore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'WIKIBASE_DATABASE_VERSION' ) ) {
	define( 'WIKIBASE_DATABASE_VERSION', '0.2 alpha' );

	if ( defined( 'MEDIAWIKI' ) ) {
		call_user_func( function() {
			require_once __DIR__ . '/WikibaseDatabase.mw.php';
		} );
	}
}
