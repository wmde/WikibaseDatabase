<?php

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

$autoloader = require_once( __DIR__ . '/../vendor/autoload.php' );

$autoloader->addClassMap( array(
	"Wikibase\\Database\\Tests\\PDO\\PDOMock" => __DIR__ . "/phpunit/PDO/PDOMock.php"
) );