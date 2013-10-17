#! /bin/bash

set -x

if [ "$1" == "Standalone" ]
then
	composer install
else
	cd ..

	git clone https://gerrit.wikimedia.org/r/p/mediawiki/core.git phase3 --depth 1

	cd -
	cd ../phase3/extensions

	mkdir WikibaseDatabase

	cd -
	cp -r * ../phase3/extensions/WikibaseDatabase

	cd ../phase3

	mysql -e 'create database its_a_mw;'
	php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin

	cd extensions/WikibaseDatabase
	composer install

	cd ../..
	echo 'require_once( __DIR__ . "/extensions/WikibaseDatabase/WikibaseDatabase.php" );' >> LocalSettings.php

	echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
	echo 'ini_set("display_errors", 1);' >> LocalSettings.php
	echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
	echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php
	echo '$wgDBprefix = "mw_";' >> LocalSettings.php

	php maintenance/update.php --quick

fi
