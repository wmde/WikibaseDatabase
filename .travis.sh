#! /bin/bash

set -x

if [ "$1" == "yeah" ]
then
	phpunit --testsuite=WikibaseDatabaseStandalone
else
	cd ..
	pwd
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/core.git phase3 --depth 1
	cd phase3
	mysql -e 'create database its_a_mw;'
	php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin
	cd extensions
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/extensions/WikibaseDatabase.git
	cd WikibaseDatabase
	phpunit
fi
