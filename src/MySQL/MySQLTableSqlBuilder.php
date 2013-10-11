<?php

namespace Wikibase\Database\MySQL;

use RuntimeException;
use Wikibase\Database\Escaper;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableSqlBuilder;
use Wikibase\Database\TableNameFormatter;

/**
 * MySQL implementation of TableSqlBuilder.
 *
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class MySQLTableSqlBuilder extends TableSqlBuilder {

	protected $dbName;
	protected $escaper;
	protected $tableNameFormatter;
	protected $fieldSqlBuilder;

	/**
	 * @param string $dbName
	 * @param Escaper $fieldValueEscaper
	 * @param TableNameFormatter $tableNameFormatter
	 */
	public function __construct( $dbName, Escaper $fieldValueEscaper, TableNameFormatter $tableNameFormatter  ) {
		$this->dbName = $dbName;
		$this->escaper = $fieldValueEscaper;
		$this->tableNameFormatter = $tableNameFormatter;
		//TODO inject sqlbuilder
		$this->fieldSqlBuilder = new MySQLFieldSqlBuilder( $this->escaper );
	}

	/**
	 * @see ExtendedAbstraction::createTable
	 *
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 *
	 * @return string
	 */
	public function getCreateTableSql( TableDefinition $table ) {
		$sql = 'CREATE TABLE `' . $this->dbName . '`.' .
			$this->tableNameFormatter->formatTableName( $table->getName() ) .' (';

		$queryParts = array();

		foreach ( $table->getFields() as $field ) {
			$queryParts[] = $this->fieldSqlBuilder->getFieldSQL( $field );
		}

		foreach ( $table->getIndexes() as $index ){
			$queryParts[] = $this->getIndexSQL( $index );
		}

		$sql .= implode( ', ', $queryParts );

		$sql .= ') ' . 'ENGINE=InnoDB, DEFAULT CHARSET=binary;';

		return $sql;
	}

	/**
	 * @since 0.1
	 *
	 * @param IndexDefinition $index
	 *
	 * @return string
	 * @todo use a MySQLIndexSqlBuilder (although this creates indexes in a seperate query)
	 */
	protected function getIndexSQL( IndexDefinition $index ) {
		$sql = $this->getIndexType( $index->getType() );

		if( $index->getType() !== IndexDefinition::TYPE_PRIMARY ){
			//todo escape name once identifier escaping is implemented
			$sql .= ' `'.$index->getName().'`';
		}

		$columnNames = array();
		//TODO FIXME Error: 1170 BLOB/TEXT column 'textfield' used in key specification without a key length (localhost)
		//todo $intSize here needs to specify the length of the key for text fields.
		foreach( $index->getColumns() as $columnName => $intSize ){
			$columnNames[] = $columnName;
		}

		$sql .= ' (`'.implode( '`,`', $columnNames ).'`)';

		return $sql;
	}

	/**
	 * Returns the MySQL field type for a given IndexDefinition type constant.
	 *
	 * @param string $indexType
	 *
	 * @return string
	 * @throws RuntimeException
	 * @todo use a MySQLIndexSqlBuilder (although this creates indexes in a seperate query)
	 */
	protected function getIndexType( $indexType ) {
		switch ( $indexType ) {
			case IndexDefinition::TYPE_PRIMARY:
				return 'PRIMARY KEY';
			case IndexDefinition::TYPE_INDEX:
				return 'INDEX';
			case IndexDefinition::TYPE_UNIQUE:
				return 'UNIQUE INDEX';
			case IndexDefinition::TYPE_SPATIAL:
				return 'SPATIAL INDEX';
			case IndexDefinition::TYPE_FULLTEXT:
				return 'FULLTEXT INDEX';
			default:
				throw new RuntimeException( __CLASS__ . ' does not support db indexes of type ' . $indexType );
		}
	}

}
