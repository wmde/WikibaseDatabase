<?php

/**
 * MediaWiki setup for the Database component of Wikibase.
 * The component should be included via the main entry point, Database.php.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'WIKIBASE_DATABASE_VERSION' ) ) {
	die( 'Not an entry point.' );
}

global $wgExtensionCredits, $wgExtensionMessagesFiles, $wgHooks;

$wgExtensionCredits['wikibase'][] = array(
	'path' => __DIR__,
	'name' => 'Wikibase Database',
	'version' => WIKIBASE_DATABASE_VERSION,
	'author' => array(
		'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
	),
	'url' => 'https://www.mediawiki.org/wiki/Extension:Wikibase_Database',
	'descriptionmsg' => 'wikibasedatabase-desc'
);

$wgExtensionMessagesFiles['WikibaseDatabase'] = __DIR__ . '/WikibaseDatabase.i18n.php';

if ( defined( 'MW_PHPUNIT_TEST' ) ) {
	require_once __DIR__ . '/tests/testLoader.php';
}

/**
 * Hook to add PHPUnit test cases.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
 *
 * @since 0.1
 *
 * @param array $files
 *
 * @return boolean
 */
$wgHooks['UnitTestsList'][]	= function( array &$files ) {
	// @codeCoverageIgnoreStart
	$testFiles = array(
		'MWDB/ExtendedMySQLAbstraction',

		'FieldDefinition',
		'MediaWikiQueryInterface',
		'ResultIterator',
		'TableBuilder',
		'TableDefinition',
		'TableCreationFailedException',
	);

	foreach ( $testFiles as $file ) {
		$files[] = __DIR__ . '/tests/phpunit/' . $file . 'Test.php';
	}

	return true;
	// @codeCoverageIgnoreEnd
};
