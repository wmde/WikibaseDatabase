<?php

namespace Wikibase\Database\Schema\Definitions;

use InvalidArgumentException;

/**
 * Definition of a database table. Immutable.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableDefinition {

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @since 0.1
	 *
	 * @var FieldDefinition[]
	 */
	private $fields;

	/**
	 * @since 0.1
	 *
	 * @var IndexDefinition[]
	 */
	private $indexes;

	/**
	 * @since 0.1
	 *
	 * @param string $name
	 * @param FieldDefinition[] $fields
	 * @param IndexDefinition[] $indexes
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $name, array $fields, array $indexes = array() ) {
		$this->setName( $name );
		$this->setFields( $fields );
		$this->setIndexes( $indexes );
	}

	protected function setName( $name ) {
		if ( !is_string( $name ) ) {
			throw new InvalidArgumentException( 'The table $name needs to be a string' );
		}

		$this->name = $name;
	}

	/**
	 * @param FieldDefinition[] $fields
	 *
	 * @throws InvalidArgumentException
	 */
	protected function setFields( array $fields ) {
		if ( empty( $fields ) ) {
			throw new InvalidArgumentException( 'The table $fields list cannot be empty' );
		}

		$this->fields = array();

		foreach ( $fields as $field ) {
			if ( !( $field instanceof FieldDefinition ) ) {
				throw new InvalidArgumentException( 'All table fields should be of type FieldDefinition' );
			}

			if ( array_key_exists( $field->getName(), $this->fields ) ) {
				throw new InvalidArgumentException( 'A table cannot have two fields with the same name' );
			}

			$this->fields[$field->getName()] = $field;
		}
	}

	/**
	 * @param IndexDefinition[] $indexes
	 *
	 * @throws InvalidArgumentException
	 */
	protected function setIndexes( array $indexes ) {
		$this->indexes = array();

		foreach ( $indexes as $index ) {
			if ( !( $index instanceof IndexDefinition ) ) {
				throw new InvalidArgumentException( 'All table indexes should be of type IndexDefinition' );
			}

			$this->indexes[$index->getName()] = $index;
		}
	}

	/**
	 * Returns the name of the table.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the fields that make up this table.
	 * The array keys in the returned array correspond to the names
	 * of the fields defined by the value they point to.
	 *
	 * @since 0.1
	 *
	 * @return FieldDefinition[]
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Returns the indexes that this table has.
	 * The array keys in the returned array correspond to the names
	 * of the indexes defined by the value they point to.
	 *
	 * @since 0.1
	 *
	 * @return IndexDefinition[]
	 */
	public function getIndexes() {
		return $this->indexes;
	}

	/**
	 * Returns if the table has a field with the provided name.
	 *
	 * @since 0.1
	 *
	 * @param string $fieldName
	 *
	 * @return boolean
	 */
	public function hasFieldWithName( $fieldName ) {
		return array_key_exists( $fieldName, $this->fields );
	}

	/**
	 * Returns a clone of the table, though with the provided name instead.
	 *
	 * @since 0.1
	 *
	 * @param string $cloneName
	 *
	 * @return TableDefinition
	 */
	public function mutateName( $cloneName ) {
		return new self( $cloneName, $this->fields, $this->indexes );
	}

	/**
	 * Returns a clone of the table, though with the provided fields rather then the original ones.
	 *
	 * @since 0.1
	 *
	 * @param FieldDefinition[] $fields
	 *
	 * @return TableDefinition
	 */
	public function mutateFields( array $fields ) {
		return new self( $this->name, $fields, $this->indexes );
	}

	/**
	 * Returns a clone of the table, though with the provided field removed.
	 *
	 * @since 0.1
	 *
	 * @param string $fieldName
	 *
	 * @return TableDefinition
	 */
	public function mutateFieldAway( $fieldName ){
		$newFields = array();
		foreach( $this->getFields() as $field ){
			if( $field->getName() !== $fieldName ){
				$newFields[] = $field;
			}
		}
		return $this->mutateFields( $newFields );
	}

	/**
	 * Returns a clone of the table, though with the provided indexes rather then the original ones.
	 *
	 * @since 0.1
	 *
	 * @param IndexDefinition[] $indexes
	 *
	 * @return TableDefinition
	 */
	public function mutateIndexes( array $indexes ) {
		return new self( $this->name, $this->fields, $indexes );
	}

	/**
	 * Returns a clone of the table, though with the provided index removed.
	 *
	 * @since 0.1
	 *
	 * @param string $indexName
	 *
	 * @return TableDefinition
	 */
	public function mutateIndexAway( $indexName ){
		$newIndexes = array();
		foreach( $this->getIndexes() as $index ){
			if( $index->getName() !== $indexName ){
				$newIndexes[] = $index;
			}
		}
		return $this->mutateIndexes( $newIndexes );
	}

}
