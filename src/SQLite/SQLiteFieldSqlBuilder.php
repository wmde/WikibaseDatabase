<?php

namespace Wikibase\Database\SQLite;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\FieldSqlBuilder;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class SQLiteFieldSqlBuilder extends FieldSqlBuilder {

	protected $escaper;

	/**
	 * @param Escaper $escaper
	 */
	public function __construct( Escaper $escaper ) {
		$this->escaper = $escaper;
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#column-def
	 */
	public function getFieldSQL( FieldDefinition $field ){
		$sql =  $this->escaper->getEscapedIdentifier( $field->getName() ) . ' ';

		$sql .= $this->getFieldType( $field->getType() );

		$sql .= $this->getDefault( $field->getDefault(), $field->getType() );

		$sql .= $this->getNull( $field->allowsNull() );

		$sql .= $this->getAutoInc( $field->hasAutoIncrement() );

		return $sql;
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#column-constraint
	 */
	protected function getDefault( $default, $type ) {
		if ( $default !== null ) {
			//TODO ints shouldn't have quotes added to them so we can not use the escaper used for strings below???
			if( $type === FieldDefinition::TYPE_INTEGER ){
				return ' DEFAULT ' . $default;
			}
			return ' DEFAULT ' . $this->escaper->getEscapedValue( $default );
		}

		return '';
	}

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#column-constraint
	 */
	protected function getNull( $allowsNull ) {
		return $allowsNull ? ' NULL' : ' NOT NULL';
	}

	/**
	 * Returns the MySQL field type for a given FieldDefinition type constant.
	 *
	 * @see http://www.sqlite.org/syntaxdiagrams.html#type-name
	 *
	 * @param string $fieldType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldType( $fieldType ) {
		switch ( $fieldType ) {
			case FieldDefinition::TYPE_INTEGER:
				return 'INTEGER';
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

	/**
	 * @see http://www.sqlite.org/syntaxdiagrams.html#column-constraint
	 */
	protected function getAutoInc( $shouldAutoInc ){
		if ( $shouldAutoInc ){
			return ' PRIMARY KEY AUTOINCREMENT';
		}
		return '';
	}

}