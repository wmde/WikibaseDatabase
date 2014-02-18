<?php

namespace Wikibase\Database\MySQL;

use Iterator;
use RuntimeException;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\Database\Schema\SchemaReadingException;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\Database\TableNameFormatter;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MySQLTableDefinitionReader implements TableDefinitionReader {

	protected $queryInterface;
	protected $tableNameFormatter;

	public function __construct( QueryInterface $queryInterface, TableNameFormatter $tableNameFormatter ) {
		$this->queryInterface = $queryInterface;
		$this->tableNameFormatter = $tableNameFormatter;
	}

	/**
	 * @see TableDefinitionReader::readDefinition
	 *
	 * @param string $tableName
	 *
	 * @throws SchemaReadingException
	 * @return TableDefinition
	 */
	public function readDefinition( $tableName ) {
		if( !$this->queryInterface->tableExists( $tableName ) ) {
			throw new SchemaReadingException( "Unknown table {$tableName}" );
		}

		$fields = $this->getFields( $tableName );
		$indexes = $this->getIndexes( $tableName );
		return new TableDefinition( $tableName, $fields, $indexes );
	}

	/**
	 * @param string $tableName
	 * @return FieldDefinition[]
	 */
	private function getFields( $tableName ) {
		$fieldResults = $this->doColumnsQuery( $tableName );

		$fields = array();
		foreach( $fieldResults as $field ){

			$type = $this->getTypeDefinition( $field->type );

			$fields[] = new FieldDefinition(
				$field->name,
				$type,
				$this->getNullable( $field->cannull ),
				$this->getDefaultForTypeName( $field->defaultvalue, $type->getName() ),
				$this->getAutoInc( $field->extra )
			);
		}

		return $fields;
	}

	/**
	 * Performs a request to get information needed to construct FieldDefinitions
	 * @see http://dev.mysql.com/doc/refman/5.7/en/columns-table.html
	 *
	 * @param string $tableName
	 * @return Iterator
	 */
	private function doColumnsQuery( $tableName ) {
		return $this->queryInterface->select(
			'INFORMATION_SCHEMA.COLUMNS',
			array(
				'name' => 'COLUMN_NAME',
				'cannull' => 'IS_NULLABLE',
				'type' => 'DATA_TYPE',
				'defaultvalue' => 'COLUMN_DEFAULT',
				'extra' => 'EXTRA'
			),
			$this->tableNameIs( $tableName )
		);
	}

	/**
	 * @param string $type
	 *
	 * @return TypeDefinition
	 */
	private function getTypeDefinition( $type ) {
		return new TypeDefinition(
			$this->getTypeName( $type ),
			$this->getTypeSize( $type ),
			TypeDefinition::NO_ATTRIB //todo READ ATTRIBUTES
		);
	}

	/**
	 * Simplifies the datatype and returns something a TypeDefinition can expect
	 *
	 * @param string $type
	 *
	 * @throws RuntimeException
	 * @return string
	 */
	private function getTypeName( $type ) {
		list( $typePart ) = explode( '(', $type );
		switch( strtolower( $typePart ) ) {
			case 'blob':
				return TypeDefinition::TYPE_BLOB;
			case 'tinyint':
				return TypeDefinition::TYPE_TINYINT;
			case 'int':
				return TypeDefinition::TYPE_INTEGER;
			case 'decimal':
				return TypeDefinition::TYPE_DECIMAL;
			case 'bigint':
				return TypeDefinition::TYPE_BIGINT;
			case 'float':
				return TypeDefinition::TYPE_FLOAT;
			case 'varchar':
				return TypeDefinition::TYPE_VARCHAR;
		}
		throw new RuntimeException( __CLASS__ . ' does not support db fields of type ' . $type );
	}

	/**
	 * Gets the size from a type
	 *
	 * @param string $type
	 *
	 * @return int|null
	 */
	private function getTypeSize( $type ) {
		if( preg_match( '/^\w+\((\d+)\)$/', $type, $matches ) ) {
			return intval( $matches[1] );
		}
		return TypeDefinition::NO_SIZE;
	}

	/**
	 * @param string $nullable
	 * @return bool
	 */
	private function getNullable( $nullable ) {
		if( $nullable === 'YES' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param string $default
	 * @param string $typeName
	 *
	 * @return mixed
	 */
	private function getDefaultForTypeName( $default, $typeName ) {
		if( ( $typeName === TypeDefinition::TYPE_INTEGER
				|| $typeName === TypeDefinition::TYPE_BIGINT
				|| $typeName === TypeDefinition::TYPE_TINYINT )
			&& $default !== FieldDefinition::NO_DEFAULT
		) {
			return intval( $default );
		}
		return $default;
	}

	/**
	 * @param string $extra
	 * @return bool
	 */
	private function getAutoInc( $extra ){
		if( strstr( $extra, 'auto_increment' ) ){
			return FieldDefinition::AUTOINCREMENT;
		}
		return FieldDefinition::NO_AUTOINCREMENT;
	}

	/**
	 * @param $tableName string
	 * @return IndexDefinition[]
	 * @TODO support currently don't notice FULLTEXT or SPATIAL indexes
	 */
	private function getIndexes( $tableName ) {
		$resultingIndexes = array();

		$constraintsResult =  $this->doConstraintsQuery( $tableName );
		$constraintsArray = array();

		foreach( $constraintsResult as $constraint ) {
			$constraintsArray[ $constraint->name ][ $constraint->columnName ] = $this->getIndexSizeFromSubPart( $constraint->subPart );
		}

		foreach( $constraintsArray as $name => $cols ){
			$resultingIndexes[] = $this->getConstraint( $name, $cols );
		}

		$indexesResult = $this->doIndexesQuery( $tableName );
		$indexesArray = array();

		foreach( $indexesResult as $index ) {
			// Ignore any Indexes we already have (primary and unique).
			if( !array_key_exists( $index->indexName, $constraintsArray ) ){
				$indexesArray[ $index->indexName ][ $index->colName ] = $this->getIndexSizeFromSubPart( $index->subPart );
			}
		}

		foreach( $indexesArray as $name => $cols ){
			$resultingIndexes[] = $this->getIndex( $name, $cols );
		}

		return $resultingIndexes;
	}

	/**
	 * @param string $subPart value of STATISTICS.SUB_PART
	 *
	 * @return int
	 */
	private function getIndexSizeFromSubPart( $subPart ) {
		if( is_null( $subPart ) ) {
			return 0;
		} else {
			return intval( $subPart );
		}
	}

	private function getConstraint( $name, $columns ) {
		if( $name === 'PRIMARY' ){
			return new IndexDefinition( 'PRIMARY' , $columns , IndexDefinition::TYPE_PRIMARY );
		} else {
			return new IndexDefinition( $name , $columns , IndexDefinition::TYPE_UNIQUE );
		}
	}

	private function getIndex( $name, $columns ) {
		return new IndexDefinition( $name, $columns , IndexDefinition::TYPE_INDEX );
	}

	/**
	 * Performs a request to get information needed to construct IndexDefinitions
	 * for Primary Keys and Unique Indexes from constraints
	 * @see http://dev.mysql.com/doc/refman/5.7/en/key-column-usage-table.html
	 *
	 * @param string $tableName
	 * @return Iterator
	 */
	private function doConstraintsQuery( $tableName ) {
		return $this->queryInterface->select(
			array( 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE', 'INFORMATION_SCHEMA.STATISTICS' ),
			array(
				'name' => 'KEY_COLUMN_USAGE.CONSTRAINT_NAME',
				'columnName' => 'KEY_COLUMN_USAGE.COLUMN_NAME',
				'subPart' => 'STATISTICS.SUB_PART',
			),
			array_merge(
				$this->tableNameIs( $tableName, 'KEY_COLUMN_USAGE.TABLE_NAME' ),
				array(
					'KEY_COLUMN_USAGE.COLUMN_NAME = STATISTICS.COLUMN_NAME',
					'STATISTICS.INDEX_NAME = KEY_COLUMN_USAGE.CONSTRAINT_NAME',
				)
			)
		);
	}

	/**
	 * Performs a request to get information needed to construct IndexDefinitions
	 * that are not Primary Keys or Unique Indexes from statistics
	 * @see http://dev.mysql.com/doc/refman/5.7/en/statistics-table.html
	 *
	 * @param string $tableName
	 * @return Iterator
	 */
	private function doIndexesQuery( $tableName ){
		return $this->queryInterface->select(
			'INFORMATION_SCHEMA.STATISTICS',
			array(
				'colName' => 'COLUMN_NAME',
				'indexName' => 'INDEX_NAME',
				'subPart' => 'SUB_PART',
			),
			$this->tableNameIs( $tableName )
		);
	}

	/**
	 * @param string $tableName table name value we are looking for
	 * @param string $sourceTable table name of the table col we are querying
	 *
	 * @return array
	 */
	protected function tableNameIs( $tableName, $sourceTable = 'TABLE_NAME' ) {
		return array(
			$sourceTable => $this->tableNameFormatter->formatTableName( $tableName )
		);
	}

}
