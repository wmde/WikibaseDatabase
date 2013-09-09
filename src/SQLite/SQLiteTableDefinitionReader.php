<?php

namespace Wikibase\Database\SQLite;

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
class SQLiteTableDefinitionReader implements TableDefinitionReader {

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
		$keys = $this->getPrimaryKeys( $tableName );
		return new TableDefinition( $tableName, $fields, array_merge( $indexes, $keys ) );
	}

	private function getFields( $tableName ) {
		$results = $this->queryInterface->select(
			'sqlite_master',
			array( 'name', 'sql' ),
			array( 'type' => 'table', 'tbl_name' => $tableName )
		);

		if( iterator_count( $results ) > 1 ){
			throw new QueryInterfaceException( "More than one set of fields returned for {$tableName}" );
		}
		$fields = array();

		foreach( $results as $result ){
			preg_match( '/CREATE TABLE ([^ ]+) \(([^\)]+)\)/', $result['sql'], $createParts );
			/** 1 => tableName, 2 => fieldParts (fields, keys, etc.) */

			foreach( explode( ',', $createParts[2] ) as $fieldSql ){
				if( preg_match( '/([^ ]+) ([^ ]+)( DEFAULT ([^ ]+))?( ((NOT )?NULL))?/', $fieldSql, $fieldParts ) &&
					$fieldParts[0] !== 'PRIMARY KEY' ){
					/** 1 => column, 2 => type, 4 => default, 6 => NotNull */

					$type = $fieldParts[2];
					switch ( $type ) {
						case 'BOOL':
							$type = 'bool';
							break;
						case 'BLOB':
							$type = 'str';
							break;
						case 'INT':
							$type = 'int';
							break;
						case 'FLOAT':
							$type = 'float';
							break;
					}

					if( !empty( $fieldParts[4] ) ){
						$default = $fieldParts[4];
					} else {
						$default = null;
					}

					if( $fieldParts[6] === 'NOT NULL' ){
						$null = false;
					} else {
						$null = true;
					}

					$fields[] = new FieldDefinition( $fieldParts[1], $type, $null, $default );
				}
			}
		}

		return $fields;
	}

	private function getIndexes( $tableName ) {
		$results = $this->queryInterface->select(
			'sqlite_master',
			array( 'name', 'sql' ),
			array( 'type' => 'index', 'tbl_name' => $tableName )
		);
		$indexes = array();

		foreach( $results as $result ){
			preg_match( '/CREATE ([^ ]+) ([^ ]+) ON ([^ ]+) \((.+)\)\z/', $result['sql'], $createParts );
			$parsedColumns = explode( ',', $createParts[4] );
			$columns = array();
			foreach( $parsedColumns as $columnName ){
				//default unrestricted index size limit
				$columns[ $columnName ] = 0;
			}
			$indexes[] = new IndexDefinition( $createParts[2], $columns , strtolower( $createParts[1] ) );
		}

		return $indexes;
	}

	private function getPrimaryKeys( $tableName ) {
		$keys = array();
		$results = $this->queryInterface->select(
			'sqlite_master',
			array( 'name', 'sql' ),
			array( 'type' => 'table', 'tbl_name' => $tableName, "instr(sql, 'PRIMARY KEY') > 0" )
		);

		foreach( $results as $result ){
			if( preg_match( '/PRIMARY KEY \(([^\)]+)\)/', $result['sql'], $createParts ) ){
				/**  0 => PRIMARY KEY (column1, column2), 1 => column1, column2 */
				$parsedColumns = explode( ',', $createParts[1] );
				$columns = array();
				foreach( $parsedColumns as $columnName ){
					//default unrestricted index size limit
					$columns[ trim( $columnName ) ] = 0;
				}
				$keys[] = new IndexDefinition( 'PRIMARY', $columns , IndexDefinition::TYPE_PRIMARY );
			}
		}

		return $keys;
	}

}
