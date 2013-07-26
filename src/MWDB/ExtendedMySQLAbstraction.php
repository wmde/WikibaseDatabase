<?php

namespace Wikibase\Database\MWDB;

use Wikibase\Database\TableDefinition;
use Wikibase\Database\FieldDefinition;
use RuntimeException;

/**
 * MySQL implementation of ExtendedAbstraction.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ExtendedMySQLAbstraction extends ExtendedAbstraction {

	/**
	 * @see ExtendedAbstraction::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	protected function getType() {
		return 'mysql';
	}

	/**
	 * @see ExtendedAbstraction::createTable
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @return boolean Success indicator
	 */
	public function createTable( TableDefinition $table ) {
		$db = $this->getDB();

		// TODO: Escape table name?
		// TODO: get rid of global (DatabaseBase currently provides no access to its mTablePrefix field)
		$sql = 'CREATE TABLE `' . $db->getDBname() . '`.' . $GLOBALS['wgDBprefix'] . $table->getName() . ' (';

		$fields = array();

		foreach ( $table->getFields() as $field ) {
			$fields[] = $field->getName() . ' ' . $this->getFieldSQL( $field );
		}

		$sql .= implode( ',', $fields );

		// TODO: table options
		$sql .= ') ' . 'ENGINE=InnoDB, DEFAULT CHARSET=binary';

		$success = $db->query( $sql, __METHOD__ );

		return $success !== false;
	}

	/**
	 * @since 0.1
	 *
	 * @param FieldDefinition $field
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldSQL( FieldDefinition $field ) {
		$sql = $this->getFieldType( $field->getType() );

		$sql .= ' ' . $this->getDefault( $field->getDefault() );

		$sql .= ' ' . $this->getNull( $field->allowsNull() );

		if($field->getIndex()==FieldDefinition::INDEX_PRIMARY && $field->getName()!=='id') {q($field);}
		$sql .= ' ' . $this->getIndexString( $field->getIndex() );

		// TODO: add all field stuff relevant here

		return $sql;
	}

	protected function getDefault( $default ) {
		if ( $default !== null ) {
			return 'DEFAULT ' . $this->getDB()->addQuotes( $default );
		}

		return '';
	}

	protected function getNull( $allowsNull ) {
		return $allowsNull ? 'NULL' : 'NOT NULL';
	}

	/**
	 * Returns the MySQL field type for a given FieldDefinition type constant.
	 *
	 * @param string $fieldType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldType( $fieldType ) {
		switch ( $fieldType ) {
			case FieldDefinition::TYPE_INTEGER:
				return 'INT';
			case FieldDefinition::TYPE_FLOAT:
				return 'FLOAT';
			case FieldDefinition::TYPE_TEXT:
				return 'BLOB';
			case FieldDefinition::TYPE_BOOLEAN:
				return 'TINYINT';
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db fields of type ' . $fieldType );
		}
	}

	protected function getIndexString( $indexType ) {
		switch ( $indexType ) {
			case FieldDefinition::INDEX_PRIMARY:
				return 'PRIMARY KEY AUTO_INCREMENT';
		}

		// TODO: handle other index types

		return '';
	}

}
