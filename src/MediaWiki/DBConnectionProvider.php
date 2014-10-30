<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;

/**
 * Interface for database connection providers.
 *
 * TODO: specify connection handling requirements
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface DBConnectionProvider {

	/**
	 * Returns the database connection.
	 * Initialization of this connection is done if it was not already initialized.
	 *
	 * @since 0.1
	 *
	 * @return DatabaseBase
	 */
	public function getConnection();

	/**
	 * Releases the connection if doing so makes any sense resource wise.
	 *
	 * @since 0.1
	 */
	public function releaseConnection();

}
