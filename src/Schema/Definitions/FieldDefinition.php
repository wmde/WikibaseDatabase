<?php

namespace Wikibase\Database\Schema\Definitions;

use InvalidArgumentException;

/**
 * Definition of a database table field. Immutable.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class FieldDefinition {

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @since 0.2
	 *
	 * @var TypeDefinition
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
	 * @var boolean
	 */
	private $null;

	/**
	 * @since 0.1
	 *
	 * @var boolean
	 */
	private $autoIncrement;

	const NOT_NULL = false;
	const NULL = true;

	const NO_DEFAULT = null;

	const AUTOINCREMENT = true;
	const NO_AUTOINCREMENT = false;

	/**
	 * @since 0.1
	 *
	 * @param string $name
	 * @param TypeDefinition|string $type
	 * @param boolean $null
	 * @param mixed $default
	 * @param boolean $autoIncrement
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $name, $type, $null = self::NULL, $default = self::NO_DEFAULT, $autoIncrement = self::NO_AUTOINCREMENT ) {
		$this->assertIsValidName( $name );
		$this->assertIsValidType( $type );
		$this->assertIsValidNull( $null );
		$this->assertIsValidAutoIncrement( $autoIncrement );

		if( is_string( $type ) ) {
			$type = new TypeDefinition( $type );
		}

		$this->name = $name;
		$this->type = $type;
		$this->default = $default;
		$this->null = $null;
		$this->autoIncrement = $autoIncrement;
	}

	private function assertIsValidName( $name ) {
		if ( !is_string( $name ) ) {
			throw new InvalidArgumentException( 'The field $name needs to be a string' );
		}
		//TODO: fail on crazy names (containing e.g. spaces) even if the DB supports that.
	}

	private function assertIsValidType( $type ) {
		if ( !$type instanceof TypeDefinition && !is_string( $type ) ) {
			throw new InvalidArgumentException( 'The field $type needs to be a TypeDefinition instance or a string' );
		}
	}

	private function assertIsValidNull( $null ) {
		if ( !is_bool( $null ) ) {
			throw new InvalidArgumentException( 'The $null parameter needs to be a boolean' );
		}
	}

	private function assertIsValidAutoIncrement( $autoIncrement ) {
		if ( !is_bool( $autoIncrement ) ) {
			throw new InvalidArgumentException( 'The $autoIncrement parameter needs to be a boolean' );
		}
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
	 *
	 * @since 0.2
	 *
	 * @return TypeDefinition
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
