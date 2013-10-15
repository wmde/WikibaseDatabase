<?php

namespace Wikibase\Database\MediaWiki;

use Exception;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\TableNameFormatter;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiTableNameFormatter implements TableNameFormatter {

	protected $connectionProvider;

	public function __construct( DBConnectionProvider $connectionProvider ) {
		$this->connectionProvider = $connectionProvider;
	}

	public function formatTableName( $tableName ) {
		$db = $this->connectionProvider->getConnection();

		try {
			$tableName = $db->tableName( $tableName, 'raw' );
		}
		catch ( Exception $ex ) {
			$this->connectionProvider->releaseConnection();
			throw $ex;
		}

		$this->connectionProvider->releaseConnection();
		return $tableName;
	}

}
