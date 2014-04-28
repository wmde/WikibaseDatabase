<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

$autoloader = require_once( __DIR__ . '/../vendor/autoload.php' );

$autoloader->addClassMap( array(
	"Wikibase\\Database\\Tests\\PDO\\PDOStub" => __DIR__ . "/phpunit/PDO/PDOStub.php",
	"Wikibase\\Database\\Tests\\PDO\\PDOIntegrationFactory" => __DIR__ . "/integration/PDO/PDOIntegrationFactory.php"
) );

$autoloader->addPsr4(
	'Wikibase\\Database\\Tests\\TestDoubles\\', __DIR__ . '/TestDoubles'
);