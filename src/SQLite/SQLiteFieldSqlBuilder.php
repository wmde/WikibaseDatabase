<?php

namespace Wikibase\Database\SQLite;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
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
	 *
	 * @param mixed $default
	 * @param TypeDefinition $type
	 *
	 * @returns string
	 */
	protected function getDefault( $default, $type ) {
		if ( $default !== null ) {
			//TODO ints shouldn't have quotes added to them so we can not use the escaper used for strings below???
			$typeName = $type->getName();
			if( $typeName === TypeDefinition::TYPE_INTEGER || $typeName === TypeDefinition::TYPE_BIGINT || $typeName === TypeDefinition::TYPE_TINYINT ){
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
	 * @param FieldDefinition $fieldType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldType( $fieldType ) {
		$fieldTypeName = $fieldType->getName();
		switch ( $fieldTypeName ) {
			case TypeDefinition::TYPE_INTEGER:
				return 'INTEGER';
			case TypeDefinition::TYPE_DECIMAL:
				return 'DECIMAL';
			case TypeDefinition::TYPE_BIGINT:
				return 'BIGINT';
			case TypeDefinition::TYPE_FLOAT:
				return 'FLOAT'; // SQLite uses REAL, not FLOAT
			case TypeDefinition::TYPE_BLOB:
				return 'BLOB';
			case TypeDefinition::TYPE_TINYINT:
				return 'TINYINT';
			case TypeDefinition::TYPE_VARCHAR:
				return 'VARCHAR' . $this->getFieldSize( $fieldType );
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db fields of type ' . $fieldTypeName );
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

	/**
	 * Returns the SQLite field type size for a given TypeDefinition
	 *
	 * @see http://www.sqlite.org/datatype3.html
	 * @note "Note that numeric arguments in parentheses that following the type name (ex: "VARCHAR(255)") are ignored by SQLite"
	 *       "SQLite does not impose any length restrictions (other than the large global SQLITE_MAX_LENGTH limit)"
	 *       We still add this to the SQL to ensure we can read back the exact definition and to be consistent!
	 *
	 * @param TypeDefinition $fieldType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	private function getFieldSize( $fieldType ) {
		$size = $fieldType->getSize();
		if( $size === null ) {
			throw new RuntimeException( __CLASS__ . ' requires fieldType of  ' . $fieldType->getName() . ' to have a size defined' );
		}
		return "({$size})";
	}

}