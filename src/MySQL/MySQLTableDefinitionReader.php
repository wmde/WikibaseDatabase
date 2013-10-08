<?php

namespace Wikibase\Database\MySQL;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\QueryInterface\QueryInterfaceException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableDefinitionReader;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MySQLTableDefinitionReader implements TableDefinitionReader {

	protected $queryInterface;

	/**
	 * @param QueryInterface $queryInterface
	 */
	public function __construct( QueryInterface $queryInterface ) {
		$this->queryInterface = $queryInterface;
	}

	/**
	 * @see TableDefinitionReader::readDefinition
	 *
	 * @param string $tableName
	 *
	 * @throws QueryInterfaceException
	 * @return TableDefinition
	 */
	public function readDefinition( $tableName ) {
		if( !$this->queryInterface->tableExists( $tableName ) ){
			throw new QueryInterfaceException( "Unknown table {$tableName}" );
		}

		$fields = $this->getFields( $tableName );
		$indexes = $this->getIndexes( $tableName );
		return new TableDefinition( $tableName, $fields, $indexes );
	}

	/**
	 * @param $tableName string
	 * @return FieldDefinition[]
	 */
	private function getFields( $tableName ) {
		$results = $this->queryInterface->select(
			'INFORMATION_SCHEMA.COLUMNS',
			array(
				'name' => 'COLUMN_NAME',
				'cannull' => 'IS_NULLABLE',
				'type' => 'DATA_TYPE',
				'defaultvalue' => 'COLUMN_DEFAULT', ),
			array( 'TABLE_NAME' => $tableName )
		);

		$fields = array();
		foreach( $results as $field ){

			$fields[] = new FieldDefinition(
				$field->name,
				$this->getDataType( $field->type ),
				$this->getNullable( $field->cannull ),
				$field->defaultvalue );
		}

		return $fields;
	}

	/**
	 * @param $tableName string
	 * @throws QueryInterfaceException
	 * @return IndexDefinition[]
	 */
	private function getIndexes( $tableName ) {
		//TODO we currently don't notice FULLTEXT or SPATIAL indexes
		$indexes = array();

		$constraintsResult = $this->queryInterface->select(
			'INFORMATION_SCHEMA.TABLE_CONSTRAINTS',
			array(
				'name' => 'CONSTRAINT_NAME',
				'type' => 'CONSTRAINT_TYPE',
			),
			array( 'TABLE_NAME' => $tableName )
		);

		//TODO FIXME the below check for constraints will never work as $constraint->columns is undefined...
		//TODO check unit tests and write an integration test...
		foreach( $constraintsResult as $constraint ) {
			if( $constraint->type === 'PRIMARY KEY' ) {
				$type = IndexDefinition::TYPE_PRIMARY;
			} else if( $constraint->type === 'UNIQUE' ) {
				$type = IndexDefinition::TYPE_UNIQUE;
			} else {
				throw new QueryInterfaceException(
					'Unknown Constraint when reading definition ' .
					$constraint->name . ', ' . $constraint->type );
			}
			$indexes[] = new IndexDefinition( $constraint->name , $constraint->columns , $type );
		}

		$indexesResult = $this->queryInterface->select(
			'INFORMATION_SCHEMA.STATISTICS',
			array(
				'COLUMN_NAME',
				'SEQ_IN_INDEX',
				'name' => 'INDEX_NAME',
				'columns' => 'GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX)' ),
			array( 'TABLE_NAME' => $tableName ),
			array( 'GROUP BY' => 'name' )
		);

		foreach( $indexesResult as $index ){
			//ignore any indexes we already have (primary and unique)
			foreach( $constraintsResult as $constraint ){
				if( $constraint->name === $index->name ){
					continue 2;
				}
			}
			$cols = array();
			foreach( explode( ',', $index->columns ) as $col ){
				$cols[ $col ] = 0;
			}
			$indexes[] = new IndexDefinition( $index->name, $cols , IndexDefinition::TYPE_INDEX );
		}

		return $indexes;
	}

	/**
	 * Simplifies the datatype and returns something a FieldDefinition can expect
	 * @param $dataType string
	 * @return string
	 */
	private function getDataType( $dataType ) {
		if( stristr( $dataType, 'blob' ) ){
			return FieldDefinition::TYPE_TEXT;
		} else if ( stristr( $dataType, 'tinyint' ) ){
			return FieldDefinition::TYPE_BOOLEAN;
		} else if ( stristr( $dataType, 'int' ) ){
			return FieldDefinition::TYPE_INTEGER;
		} else if ( stristr( $dataType, 'float' ) ){
			return FieldDefinition::TYPE_FLOAT;
		} else {
			return $dataType;
		}
	}

	/**
	 * @param $nullable string
	 * @return bool
	 */
	private function getNullable( $nullable ) {
		if( $nullable === 'YES' ){
			return true;
		} else {
			return false;
		}
	}

}
