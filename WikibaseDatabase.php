<?php

/**
 * Entry point of the Wikibase Database component.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( defined( 'WIKIBASE_DATABASE_VERSION' ) ) {
	// Do not initialize more then once.
	return;
}

define( 'WIKIBASE_DATABASE_VERSION', '0.1' );

spl_autoload_register( function ( $className ) {
	$className = ltrim( $className, '\\' );
	$fileName = '';
	$namespace = '';

	if ( $lastNsPos = strripos( $className, '\\') ) {
		$namespace = substr( $className, 0, $lastNsPos );
		$className = substr( $className, $lastNsPos + 1 );
		$fileName  = str_replace( '\\', '/', $namespace ) . '/';
	}

	$fileName .= str_replace( '_', '/', $className ) . '.php';

	$namespaceSegments = explode( '\\', $namespace );

	$inQueryEngineNamespace = count( $namespaceSegments ) > 1
		&& $namespaceSegments[0] === 'Wikibase'
		&& $namespaceSegments[1] === 'Database';

	if ( $inQueryEngineNamespace ) {
		$inTestNamespace = count( $namespaceSegments ) > 2 && $namespaceSegments[2] === 'Tests';

		if ( !$inTestNamespace ) {
			$pathParts = explode( '/', $fileName );
			array_shift( $pathParts );
			array_shift( $pathParts );
			$fileName = implode( '/', $pathParts );

			require_once __DIR__ . '/src/' . $fileName;
		}
	}
} );

// @codeCoverageIgnoreStart
if ( defined( 'MEDIAWIKI' ) ) {
	call_user_func( function() {
		require_once __DIR__ . '/WikibaseDatabase.mw.php';
	} );
}
// @codeCoverageIgnoreEnd
