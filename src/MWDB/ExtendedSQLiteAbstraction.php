<?php

namespace Wikibase\Database\MWDB;

use Wikibase\Database\TableDefinition;
use Wikibase\Database\FieldDefinition;
use RuntimeException;

/**
 * SQLite implementation of ExtendedAbstraction.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ExtendedSQLiteAbstraction extends ExtendedAbstraction {

	/**
	 * @see ExtendedAbstraction::getType
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	protected function getType() {
		return 'sqlite';
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
		$sql = 'CREATE TABLE ' . $GLOBALS['wgDBprefix'] . $table->getName() . ' (';

		$fields = array();

		foreach ( $table->getFields() as $field ) {
			$fields[] = $field->getName() . ' ' . $this->getFieldSQL( $field );
		}

		$sql .= implode( ',', $fields );

		// TODO: table options
		$sql .= ');';

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

		if ( $field->getDefault() !== null ) {
			$sql .= ' DEFAULT ' . $this->getDB()->addQuotes( $field->getDefault() );
		}

		// TODO: add all field stuff relevant here

		$sql .= $field->allowsNull() ? ' NULL' : ' NOT NULL';

		return $sql;
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

}
