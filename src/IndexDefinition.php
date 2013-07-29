<?php

namespace Wikibase\Database;

use InvalidArgumentException;

/**
 * Definition of a database index. Immutable.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Denny Vrandecic < vrandecic@gmail.com >
 */
class IndexDefinition {

	protected $name;
	protected $columns;

	/**
	 * @param string $name
	 * @param int[] $columns array with string column names => int size, 0 for unrestricted size
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $name, $columns ) {
		$this->assertIsValidIndexName( $name );
		$this->assertAreValidColumns( $columns );

		$this->name = $name;
		$this->columns = $columns;
	}

	protected function assertIsValidIndexName( $indexName ) {
		if ( !$this->isAlphanumericOrUnderscoreString( $indexName ) ) {
			throw new InvalidArgumentException( 'The index name needs to be an alphanumeric string that can contain underscores' );
		}
	}

	protected function isAlphanumericOrUnderscoreString( $string ) {
		if ( !is_string( $string ) ) {
			return false;
		}

		if ( $string === '' ) {
			return false;
		}

		if ( !ctype_alnum( str_replace( array( '_' ), '', $string ) ) ) {
			return false;
		}

		return true;
	}

	protected function assertIsValidColumnName( $columnName ) {
		if ( !$this->isAlphanumericOrUnderscoreString( $columnName ) ) {
			throw new InvalidArgumentException( 'The column name needs to be an alphanumeric string that can contain underscores' );
		}
	}

	protected function assertAreValidColumns( $columns ) {
		if ( !is_array( $columns ) ) {
			throw new InvalidArgumentException( 'Cannot construct IndexDefinition with non-array $columns' );
		}

		if ( $columns === array() ) {
			throw new InvalidArgumentException( 'The list of columns cannot be empty' );
		}

		foreach ( $columns as $columnName => $indexSize ) {
			$this->assertIsValidColumnName( $columnName );

			if ( !is_int( $indexSize ) || ( $indexSize < 0 ) ) {
				throw new InvalidArgumentException( 'All index sizes need to be positive integers' );
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return int[] array with string column names => int size, 0 for unrestricted size
	 */
	public function getColumns() {
		return $this->columns;
	}

}
