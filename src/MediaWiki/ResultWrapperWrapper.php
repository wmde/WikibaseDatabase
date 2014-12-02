<?php

namespace Wikibase\Database\MediaWiki;

use ResultWrapper;

/**
 * Adapter that allows iterating over a result obtained via the MediaWiki database abstraction
 * layer with each result row being an array.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ResultWrapperWrapper implements \Iterator {

	private $position = 0;
	private $currentRow = null;

	private $resultWrapper;

	public function __construct( ResultWrapper $resultWrapper ) {
		$this->resultWrapper = $resultWrapper;
	}

	/**
	 * @return array|null
	 */
	public function current() {
		return $this->currentRow;
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * @return array
	 */
	public function next() {
		$this->position++;
		$this->currentRow = $this->resultWrapper->fetchRow();

		return $this->currentRow;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->current() !== false;
	}

	public function rewind() {
		$this->position = 0;
		$this->currentRow = null;
		$this->resultWrapper->rewind();
	}

}
