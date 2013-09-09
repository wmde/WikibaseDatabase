<?php

namespace Wikibase\Database\Schema\Definitions;

use InvalidArgumentException;

/**
 * Definition of a database table field. Immutable.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FieldDefinition {

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $type;

	/**
	 * @since 0.1
	 *
	 * @var mixed
	 */
	private $default;

	/**
	 * @since 0.1
	 *
	 * @var string|null
	 */
	private $attributes;

	/**
	 * @since 0.1
	 *
	 * @var boolean
	 */
	private $null;

	/**
	 * @since 0.1
	 *
	 * @var boolean
	 */
	private $autoIncrement;

	const TYPE_BOOLEAN = 'bool';
	const TYPE_TEXT = 'str';
	const TYPE_INTEGER = 'int';
	const TYPE_FLOAT = 'float';

	const NOT_NULL = false;
	const NULL = true;

	const NO_DEFAULT = null;

	const NO_ATTRIB = null;
	const ATTRIB_BINARY = 'binary';
	const ATTRIB_UNSIGNED = 'unsigned';

	const AUTOINCREMENT = true;
	const NO_AUTOINCREMENT = false;

	/**
	 * @since 0.1
	 *
	 * @param string $name
	 * @param string $type
	 * @param boolean $null
	 * @param mixed $default
	 * @param string|null $attributes
	 * @param boolean $autoIncrement
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $name, $type, $null = self::NULL, $default = self::NO_DEFAULT, $attributes = null, $autoIncrement = false ) {
		if ( !is_string( $name ) ) {
			throw new InvalidArgumentException( 'The field $name needs to be a string' );
		}

		if ( !is_string( $type ) ) {
			throw new InvalidArgumentException( 'The field $type needs to be a string' );
		}

		if ( !is_bool( $null ) ) {
			throw new InvalidArgumentException( 'The $null parameter needs to be a boolean' );
		}

		if ( !is_bool( $autoIncrement ) ) {
			throw new InvalidArgumentException( 'The $autoIncrement parameter needs to be a boolean' );
		}

		$this->name = $name;
		$this->type = $type;
		$this->default = $default;
		$this->attributes = $attributes;
		$this->null = $null;
		$this->autoIncrement = $autoIncrement;
	}

	/**
	 * Returns the name of the field.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the type of the field.
	 * This is one of the TYPE_ constants.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Returns the default value of the field.
	 * Null for no default value.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function getDefault() {
		return $this->default;
	}

	/**
	 * Returns the attributes of the field.
	 * This is one of the ATTRIB_ constants or null.
	 *
	 * @since 0.1
	 *
	 * @return string|null
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * Returns if the field allows for the value to be null.
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function allowsNull() {
		return $this->null;
	}


	/**
	 * Returns if the field has auto increment.
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function hasAutoIncrement() {
		return $this->autoIncrement;
	}

}
