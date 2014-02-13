<?php

namespace Wikibase\Database\MySQL;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
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
	 * Returns the MySQL field type for a given FieldDefinition type constant.
	 *
	 * @param string $fieldType
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	protected function getFieldType( $fieldType ) {
		switch ( $fieldType ) {
			// No datatype for short strings, i.e. VARCHAR? TEXT or BLOB fields should not be used for that.
			case FieldDefinition::TYPE_INTEGER:
				return 'INT';
			case FieldDefinition::TYPE_DECIMAL:
				return 'DECIMAL';
			case FieldDefinition::TYPE_BIGINT:
				return 'BIGINT';
			case FieldDefinition::TYPE_FLOAT:
				return 'FLOAT';
			//todo define max length of blob fields?
			case FieldDefinition::TYPE_BLOB:
				return 'BLOB'; // This is 64k max.
			case FieldDefinition::TYPE_TINYINT:
				return 'TINYINT';
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db fields of type ' . $fieldType );
		}
	}

}