<?php

/**
 * PHPUnit test bootstrap file for the Wikibase Database component.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

error_reporting( E_ALL | E_STRICT );
ini_set( 'display_errors', 1 );

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

if ( in_array( '--testsuite=WikibaseDatabaseMediaWiki', $GLOBALS['argv'] ) ) {
	global $IP;
	$IP = getenv( 'MW_INSTALL_PATH' );

	if ( $IP === false ) {
		$IP = dirname( __FILE__ ) . '/../../..';
	}

	if ( is_readable( "$IP/includes/Init.php" ) ) {
		require_once( __DIR__ . '/evilMediaWikiBootstrap.php' );
	}
	else {
		die( 'MediaWiki cannot be loaded. Run the tests with --testsuite=WikibaseDatabaseStandalone' );
	}
}

require_once( 'testLoader.php' );
