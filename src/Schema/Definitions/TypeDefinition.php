<?php

namespace Wikibase\Database\Schema\Definitions;

use InvalidArgumentException;

/**
 * Definition of a database field type
 *
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class TypeDefinition {

	/**
	 * @since 0.2
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @since 0.2
	 *
	 * @var int
	 */
	private $size;

	/**
	 * @since 0.2
	 *
	 * @var string|null
	 */
	private $attributes;

	const TYPE_TINYINT = 'tinyint';
	const TYPE_BLOB = 'blob'; // need at least short sting vs text vs blob
	const TYPE_INTEGER = 'int';
	const TYPE_DECIMAL = 'decimal';
	const TYPE_BIGINT = 'bigint';
	const TYPE_FLOAT = 'float';
	const TYPE_VARCHAR = 'varchar';

	const NO_SIZE = null;

	const NO_ATTRIB = null;
	const ATTRIB_BINARY = 'binary';
	const ATTRIB_UNSIGNED = 'unsigned';

	/**
	 * @since 0.2
	 *
	 * @param string $name
	 * @param int|null $size
	 * @param string|null $attributes
	 */
	public function __construct( $name, $size = self::NO_SIZE, $attributes = self::NO_ATTRIB ) {
		$this->assertIsValidName( $name );
		$this->assertIsValidSize( $size );

		$this->name = $name;
		$this->size = $size;
		$this->attributes = $attributes;
	}

	private function assertIsValidName( $name ) {
		if ( !is_string( $name ) ) {
			throw new InvalidArgumentException( 'The field $name needs to be a string' );
		}
		if( !in_array( $name, array(
			self::TYPE_TINYINT,
			self::TYPE_BLOB,
			self::TYPE_INTEGER,
			self::TYPE_DECIMAL,
			self::TYPE_BIGINT,
			self::TYPE_FLOAT,
			self::TYPE_VARCHAR,
		) ) ) {
			throw new InvalidArgumentException( '$name specifies an unknown type name: ' . $name );
		}
	}

	private function assertIsValidSize( $size ) {
		if ( !is_int( $size ) && $size !== self::NO_SIZE ) {
			throw new InvalidArgumentException( 'The field $size needs to be an int or TypeDefinition::::NO_SIZE' );
		}
	}

	/**
	 * Returns the identifier for the type
	 * This is one of the Type_ constants
	 *
	 * @since 0.2
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the max size for the type
	 * This is an int or NO_SIZE
	 *
	 * @since 0.2
	 *
	 * @return int|self::NO_SIZE
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Returns the attributes for the type
	 * This is one of the ATTRIB_ constants or null.
	 *
	 * @since 0.2
	 *
	 * @return string|self::NO_ATTRIB
	 */
	public function getAttributes() {
		return $this->attributes;
	}

} 