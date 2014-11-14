<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;

/**
 * Lazy database connection provider.
 * The connection is fetched when needed using the id provided in the constructor.
 *
 * TODO: implement connection handling requirements
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LazyDBConnectionProvider implements DBConnectionProvider {

	/**
	 * @var int
	 */
	private $connectionId;

	/**
	 * Query group(s), or false for the generic reader
	 *
	 * @var array|string|bool
	 */
	private $groups;

	/**
	 * Wiki ID, or false for the current wiki
	 *
	 * @var string|bool
	 */
	private $wiki;

	/**
	 * @var DatabaseBase|null
	 */
	private $connection = null;

	/**
	 * @since 0.1
	 *
	 * @param int $connectionId
	 * @param array|string|bool $groups Query group(s), or false for the generic reader
	 * @param string|bool $wiki Wiki ID, or false for the current wiki
	 */
	public function __construct( $connectionId, $groups = array(), $wiki = false ) {
		$this->connectionId = $connectionId;
		$this->groups = $groups;
		$this->wiki = $wiki;
	}

	/**
	 * @see DBConnectionProvider::getConnection
	 *
	 * @since 0.1
	 *
	 * @return DatabaseBase
	 */
	public function getConnection() {
		if ( $this->connection === null ) {
			$this->connection = wfGetLB( $this->wiki )->getConnection( $this->connectionId, $this->groups, $this->wiki );
		}

		assert( $this->connection instanceof DatabaseBase );

		return $this->connection;
	}

	/**
	 * @see DBConnectionProvider::releaseConnection
	 *
	 * @since 0.1
	 */
	public function releaseConnection() {
		if ( $this->wiki !== false && $this->connection !== null ) {
			wfGetLB( $this->wiki )->reuseConnection( $this->connection );
		}
	}

}
