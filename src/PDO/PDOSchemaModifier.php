<?php

namespace Wikibase\Database\PDO;

use PDO;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\FieldAdditionFailedException;
use Wikibase\Database\Schema\FieldRemovalFailedException;
use Wikibase\Database\Schema\IndexAdditionFailedException;
use Wikibase\Database\Schema\IndexRemovalFailedException;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;
use Wikibase\Database\Schema\SchemaModifier;

/**
 * @since 0.2
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOSchemaModifier implements SchemaModifier {

	private $pdo;
	private $sqlBuilder;

	public function __construct( PDO $pdo, SchemaModificationSqlBuilder $sqlBuilder ) {
		$this->pdo = $pdo;
		$this->sqlBuilder = $sqlBuilder;
	}

	/**
	 * @see SchemaModifier::removeField
	 *
	 * @param string $tableName
	 * @param string $fieldName
	 *
	 * @throws FieldRemovalFailedException
	 */
	public function removeField( $tableName, $fieldName ) {
		$sql = $this->sqlBuilder->getRemoveFieldSql( $tableName, $fieldName );

		foreach( explode( PHP_EOL, $sql ) as $query ) {
			$success = $this->pdo->query( $query );

			if ( $success === false ) {
				throw new FieldRemovalFailedException(
					$tableName,
					$fieldName,
					$this->getErrorMessage()
				);
			}
		}
	}

	private function getErrorMessage() {
		$errorInfo = $this->pdo->errorInfo();

		if ( is_array( $errorInfo ) ) {
			return $errorInfo[2];
		}

		return '';
	}

	/**
	 * @see SchemaModifier::addField
	 *
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * @throws FieldAdditionFailedException
	 */
	public function addField( $tableName, FieldDefinition $field ) {
		$success = $this->pdo->query( $this->sqlBuilder->getAddFieldSql( $tableName, $field ) );

		if ( $success === false ) {
			throw new FieldAdditionFailedException(
				$tableName,
				$field,
				$this->getErrorMessage()
			);
		}
	}

	/**
	 * @see SchemaModifier::removeIndex
	 *
	 * @param string $tableName
	 * @param string $indexName
	 *
	 * @throws IndexRemovalFailedException
	 */
	public function removeIndex( $tableName, $indexName ) {
		$success = $this->pdo->query( $this->sqlBuilder->getRemoveIndexSql( $tableName, $indexName ) );

		if ( $success === false ) {
			throw new IndexRemovalFailedException(
				$tableName,
				$indexName,
				$this->getErrorMessage()
			);
		}
	}

	/**
	 * @see SchemaModifier::addIndex
	 *
	 * @param string $tableName
	 * @param IndexDefinition $index
	 *
	 * @throws IndexAdditionFailedException
	 */
	public function addIndex( $tableName, IndexDefinition $index ) {
		$success = $this->pdo->query( $this->sqlBuilder->getAddIndexSql( $tableName, $index ) );

		if ( $success === false ) {
			throw new IndexAdditionFailedException(
				$tableName,
				$index,
				$this->getErrorMessage()
			);
		}
	}

}
