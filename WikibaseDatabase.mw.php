<?php

/**
 * MediaWiki setup for the Wikibase Database component.
 * The component should be included via its main entry point, Database.php.
 *
 * @codeCoverageIgnore
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'WIKIBASE_DATABASE_VERSION' ) ) {
	die( 'Not an entry point.' );
}

$GLOBALS['wgExtensionCredits']['wikibase'][] = array(
	'path' => __DIR__,
	'name' => 'Wikibase Database',
	'version' => WIKIBASE_DATABASE_VERSION,
	'author' => array(
		'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
		'Adam Shorland',
	),
	'url' => 'https://www.mediawiki.org/wiki/Extension:Wikibase_Database',
	'descriptionmsg' => 'wikibasedatabase-desc'
);

$GLOBALS['wgExtensionMessagesFiles']['WikibaseDatabase'] = __DIR__ . '/WikibaseDatabase.i18n.php';

