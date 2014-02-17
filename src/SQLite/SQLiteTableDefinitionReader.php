<?php

namespace Wikibase\Database\SQLite;

use Iterator;
use RuntimeException;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\SchemaReadingException;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\Database\TableNameFormatter;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class SQLiteTableDefinitionReader implements TableDefinitionReader {

	protected $queryInterface;
	protected $unEscaper;
	protected $tableNameFormatter;

	/**
	 * @param QueryInterface $queryInterface
	 * @param SQLiteUnEscaper $unEscaper
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct(
		QueryInterface $queryInterface,
		SQLiteUnEscaper $unEscaper,
		TableNameFormatter $tableNameFormatter
	) {
		$this->queryInterface = $queryInterface;
		$this->unEscaper = $unEscaper;
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
		if( !$this->queryInterface->tableExists( $tableName ) ){
			throw new SchemaReadingException( "Unknown table {$tableName}" );
		}

		$formattedTableName = $this->tableNameFormatter->formatTableName( $tableName );

		$fields = $this->getFields( $formattedTableName );
		$indexes = $this->getIndexes( $formattedTableName );
		$keys = $this->getPrimaryKeys( $formattedTableName );

		return new TableDefinition( $tableName, $fields, array_merge( $indexes, $keys ) );
	}

	/**
	 * Returns an array of all fields in the given table
	 *
	 * @param string $tableName
	 *
	 * @throws SchemaReadingException
	 * @return FieldDefinition[]
	 */
	private function getFields( $tableName ) {
		$results = $this->doCreateQuery( $tableName );
		if( iterator_count( $results ) !== 1 ) {
			throw new SchemaReadingException(
				"Incorrect number of CREATE TABLE sql results returned for {$tableName}" .
				"\nExpected 1, Got " . strval( iterator_count( $results ) )
			);
		}
		$fields = array();

		foreach( $results as $result ){
			$sql = preg_replace( '/, PRIMARY KEY \([^\)]+\)/', '', $result->sql );
			/** $createParts,  1 => tableName, 2 => fieldParts (fields, keys, etc.) */
			$matchedCreate = preg_match( '/CREATE TABLE ([^ ]+) \(([^\)]+)\)/', $sql, $createParts );
			if( $matchedCreate !== 1 ){
				throw new SchemaReadingException( "Failed to match CREATE TABLE regex with sql string: " . $sql );
			}

			foreach( explode( ',', $createParts[2] ) as $fieldSql ) {
				$matchedParts = preg_match( '/([^ ]+) ([^ ]+)( DEFAULT ([^ ]+))?( ((NOT )?NULL))?( (PRIMARY KEY AUTOINCREMENT))?/', $fieldSql, $fieldParts );
				if( $matchedParts !== 1 ){
					throw new SchemaReadingException( "Failed to match CREATE TABLE \$fieldSql regex with sql string: " . $fieldSql . " - parsed from : ". $sql );
				} else if( $fieldParts[0] !== 'PRIMARY KEY' ) {
					$fields[] = $this->getField( $fieldParts );
				}
			}
		}
		return $fields;
	}

	/**
	 * Performs a request to get the SQL needed to create the given table
	 * @param string $tableName
	 * @return Iterator
	 */
	private function doCreateQuery( $tableName ){
		return $this->queryInterface->select(
			'sqlite_master',
			array( 'sql' ),
			array( 'type' => 'table', 'tbl_name' => $tableName ) );
	}

	private function getField( $fieldParts ) {
		$name = $this->unEscaper->getUnEscapedIdentifier( $fieldParts[1] );
		$type = $this->getFieldType( $fieldParts[2] );
		$default = $this->getFieldDefault( $fieldParts[4] );
		$null = $this->getFieldCanNull( $fieldParts[6] );
		$attr = FieldDefinition::NO_ATTRIB; //todo read ATTRIBS

		if( array_key_exists( 9, $fieldParts ) ){
			$autoInc = $this->getAutoInc( $fieldParts[9] );
		} else {
			$autoInc = FieldDefinition::NO_AUTOINCREMENT;
		}

		return new FieldDefinition( $name, $type, $null, $default, $attr, $autoInc );
	}

	private function getFieldType( $type ) {
		switch ( $type ) {
			case 'TINYINT':
				return 'tinyint';
			case 'BIGINT':
				return 'bigint';
			case 'DECIMAL':
				return 'decimal';
			case 'BLOB':
				return 'blob';
			case 'INT':
			case 'INTEGER':
				return 'int';
			case 'FLOAT':
				return 'float';
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db fields of type ' . $type );
		}
	}

	private function getFieldDefault( $default ) {
		if( !empty( $default ) ){
			return $default;
		} else {
			return FieldDefinition::NO_DEFAULT;
		}
	}

	private function getFieldCanNull( $canNull ) {
		if( $canNull === 'NOT NULL' ){
			return FieldDefinition::NOT_NULL;
		} else {
			return FieldDefinition::NULL;
		}
	}

	private function getAutoInc( $autoInc ){
		if( $autoInc === 'PRIMARY KEY AUTOINCREMENT' ){
			return FieldDefinition::AUTOINCREMENT;
		}
		return FieldDefinition::NO_AUTOINCREMENT;
	}

	/**
	 * Returns an array of all indexes for a given table (excluding Primary Keys)
	 * @param string $tableName
	 * @return IndexDefinition[]
	 */
	private function getIndexes( $tableName ) {
		$results = $this->doIndexQuery( $tableName );
		$indexes = array();

		foreach( $results as $result ){
			$indexes[] = $this->getIndex( $result->sql );
		}

		return $indexes;
	}

	private function getIndex( $sql ){
		preg_match( '/CREATE (INDEX|UNIQUE INDEX) ([^ ]+) ON ([^ ]+) \((.+)\)\z/', $sql, $createParts );
		$parsedColumns = explode( ',', $createParts[4] );
		$columns = array();
		foreach( $parsedColumns as $columnName ){
			//default unrestricted index size limit
			$columns[ $this->unEscaper->getUnEscapedIdentifier( $columnName ) ] = 0;
		}
		$name = $this->unEscaper->getUnEscapedIdentifier( $createParts[2] );
		$type = $this->getIndexType( $createParts[1] );
		return new IndexDefinition( $name, $columns , $type );
	}

	/**
	 * Performs a request to get the SQL needed to create all indexes for a table
	 * @param string $tableName
	 * @return Iterator
	 */
	private function doIndexQuery( $tableName ){
		return $this->queryInterface->select(
			'sqlite_master',
			array( 'sql' ),
			array( 'type' => 'index', 'tbl_name' => $tableName )
		);
	}

	private function getIndexType( $type ) {
		switch ( $type ) {
			case 'INDEX':
				return IndexDefinition::TYPE_INDEX;
			case 'UNIQUE INDEX':
				return IndexDefinition::TYPE_UNIQUE;
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db indexes of type ' . $type );
		}
	}

	/**
	 * Returns an array of all primary keys for a given table
	 * @param string $tableName
	 * @return IndexDefinition[]
	 */
	private function getPrimaryKeys( $tableName ) {
		$keys = array();
		$results = $this->doPrimaryKeyQuery( $tableName );

		foreach( $results as $result ){
			$keys[] = $this->getPrimaryKey( $result->sql );
		}

		return $keys;
	}

	private function getPrimaryKey( $sql ){
		if( preg_match( '/PRIMARY KEY \(([^\)]+)\)/', $sql, $createParts ) ) {
			return $this->getPrimaryKeyForFields( $createParts[1] );
		} else if( preg_match( '/(\(|,| )+([^ ]+)[a-z0-9 _]+PRIMARY KEY AUTOINCREMENT/i', $sql, $fieldParts ) ) {
			return $this->getPrimaryKeyForField( $fieldParts[2] );
		}
		throw new RuntimeException( __CLASS__ . " can not read primary ky from sql '{$sql}'" );
	}

	/**
	 * @param array $fieldNames
	 * @return IndexDefinition
	 */
	private function getPrimaryKeyForFields( $fieldNames ) {
		$parsedColumns = explode( ',', $fieldNames );
		$columns = array();
		foreach( $parsedColumns as $columnName ){
			//default unrestricted index size limit
			$columns[ trim( $this->unEscaper->getUnEscapedIdentifier( $columnName ) ) ] = 0;
		}
		return new IndexDefinition( 'PRIMARY', $columns , IndexDefinition::TYPE_PRIMARY );
	}

	/**
	 * @param string $fieldName
	 * @return IndexDefinition
	 */
	private function getPrimaryKeyForField( $fieldName ) {
		$fieldName = $this->unEscaper->getUnEscapedIdentifier( $fieldName );
		return new IndexDefinition( 'PRIMARY', array( $fieldName => 0 ) , IndexDefinition::TYPE_PRIMARY );
	}

	/**
	 * Performs a request to get the SQL needed to create the primary key for a given table
	 * @param string $tableName
	 * @return Iterator
	 */
	private function doPrimaryKeyQuery( $tableName ) {
		return $this->queryInterface->select(
			'sqlite_master',
			array( 'sql' ),
			array( 'type' => 'table', 'tbl_name' => $tableName, "sql LIKE '%PRIMARY KEY%'" )
		);
	}

}
