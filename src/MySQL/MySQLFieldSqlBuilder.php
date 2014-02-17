<?php

namespace Wikibase\Database\MySQL;

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
class MySQLFieldSqlBuilder extends FieldSqlBuilder {

	protected $escaper;

	/**
	 * @param Escaper $escaper
	 */
	public function __construct( Escaper $escaper ) {
		$this->escaper = $escaper;
	}

	public function getFieldSQL( FieldDefinition $field ){
		$sql = $this->escaper->getEscapedIdentifier( $field->getName() ). ' ';

		$sql .= $this->getFieldType( $field->getType() );

		$sql .= $this->getDefault( $field->getDefault() );

		$sql .= $this->getNull( $field->allowsNull() );

		$sql .= $this->getAutoInc( $field->hasAutoIncrement() );

		// TODO: Field Attributes

		return $sql;
	}

	protected function getDefault( $default ) {
		if ( $default !== null ) {
			return ' DEFAULT ' . $this->escaper->getEscapedValue( $default );
		}

		return '';
	}

	protected function getNull( $allowsNull ) {
		return $allowsNull ? ' NULL' : ' NOT NULL';
	}

	protected function getAutoInc( $isAutoInc ){
		if( $isAutoInc ){
			return ' AUTO_INCREMENT';
		}
		return '';
	}

	/**
	 * Returns the MySQL field type for a given TypeDefinition
	 *
	 * @param TypeDefinition $fieldType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldType( $fieldType ) {
		$fieldTypeName = $fieldType->getName();
		switch ( $fieldTypeName ) {
			// No datatype for short strings, i.e. VARCHAR? TEXT or BLOB fields should not be used for that.
			case TypeDefinition::TYPE_INTEGER:
				return 'INT';
			case TypeDefinition::TYPE_DECIMAL:
				return 'DECIMAL';
			case TypeDefinition::TYPE_BIGINT:
				return 'BIGINT';
			case TypeDefinition::TYPE_FLOAT:
				return 'FLOAT';
			//todo define max length of blob fields?
			case TypeDefinition::TYPE_BLOB:
				return 'BLOB'; // This is 64k max.
			case TypeDefinition::TYPE_TINYINT:
				return 'TINYINT';
			case TypeDefinition::TYPE_VARCHAR:
				return 'VARCHAR' . $this->getFieldSize( $fieldType );
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db fields of type ' . $fieldTypeName );
		}
	}

	/**
	 * Returns the MySQL field type size for a given TypeDefinition
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