<?php

namespace Wikibase\Database\Schema\Definitions;

use InvalidArgumentException;

/**
 * Definition of a database index. Immutable.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 *
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Denny Vrandecic < vrandecic@gmail.com >
 * @author Adam Shorland
 */
class IndexDefinition {

	const TYPE_PRIMARY = 'primary';
	const TYPE_INDEX = 'index';
	const TYPE_UNIQUE = 'unique';
	const TYPE_SPATIAL = 'spatial';
	const TYPE_FULLTEXT = 'fulltext';

	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string[]
	 */
	protected $columns;
	/**
	 * @var string of const self::TYPE_*
	 */
	protected $type;

	/**
	 * @param string $name
	 * @param string[] $columns array of column names
	 * @param string $type Element of the IndexDefinition::TYPE_ enum.
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $name, $columns, $type = self::TYPE_INDEX ) {
		$this->assertIsValidIndexName( $name );
		$this->assertAreValidColumns( $columns );
		$this->assertIsValidIndexType( $type );

		$this->name = $name;
		$this->columns = $columns;
		$this->type = $type;
	}

	protected function assertIsValidIndexType( $type ) {
		$types = array(
			self::TYPE_PRIMARY,
			self::TYPE_INDEX,
			self::TYPE_UNIQUE,
			self::TYPE_SPATIAL,
			self::TYPE_FULLTEXT,
		);

		if ( !is_string( $type ) || !in_array( $type, $types ) ) {
			throw new InvalidArgumentException( 'Invalid index type provided' );
		}
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

		foreach ( $columns as $columnName ) {
			$this->assertIsValidColumnName( $columnName );
		}

		if( array_unique( $columns ) !== $columns ) {
			throw new InvalidArgumentException( 'The list of columns cannot contain duplicates' );
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string[] array of column names
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * @return string Element of the IndexDefinition::TYPE_ enum.
	 */
	public function getType() {
		return $this->type;
	}

}
