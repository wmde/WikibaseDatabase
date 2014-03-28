<?php

namespace Wikibase\Database\PDO;

use Iterator;
use PDOStatement;

/**
 * TODO: test
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOResult implements Iterator {

	protected $iterator;

	public function __construct( PDOStatement $pdoResult ) {
		// This is currently implemented without much regard to performance.
		// A more efficient adaptation from Traversable to Iterator can be made.
		$rows = array();

		foreach ( $pdoResult as $row ) {
			$rows[] = $row;
		}

		$this->iterator = new \ArrayIterator( $rows );
	}

	/**
	 * Returns the current database row.
	 * The row is returned as an object that
	 *
	 * @see Iterator::current
	 *
	 * @return object
	 */
	public function current() {
		return (object)$this->iterator->current();
	}

	/**
	 * Move forward to next element.
	 * @see Iterator::next
	 */
	public function next() {
		$this->iterator->next();
	}

	/**
	 * Return the key of the current element.
	 * @see Iterator::key
	 *
	 * @return int
	 */
	public function key() {
		return $this->iterator->key();
	}

	/**
	 * Checks if current position is valid.
	 * @see Iterator::valid
	 *
	 * @return boolean
	 */
	public function valid() {
		return $this->iterator->current();
	}

	/**
	 * Rewind the Iterator to the first element.
	 * @see Iterator::rewind
	 */
	public function rewind() {
		$this->iterator->rewind();
	}

}
