<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\FieldAdditionFailedException;
use Wikibase\Database\Schema\FieldRemovalFailedException;
use Wikibase\Database\Schema\IndexAdditionFailedException;
use Wikibase\Database\Schema\IndexRemovalFailedException;
use Wikibase\Database\Schema\SchemaModificationSqlBuilder;
use Wikibase\Database\Schema\SchemaModifier;

/**
 * @since 0.1
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiSchemaModifier implements SchemaModifier {

	private $connectionProvider;
	private $sqlBuilder;

	/**
	 * @since 0.1
	 *
	 * @param DBConnectionProvider $connectionProvider
	 * @param SchemaModificationSqlBuilder $sqlBuilder
	 */
	public function __construct( DBConnectionProvider $connectionProvider, SchemaModificationSqlBuilder $sqlBuilder ) {
		$this->connectionProvider = $connectionProvider;
		$this->sqlBuilder = $sqlBuilder;
	}

	/**
	 * @return DatabaseBase
	 */
	private function getDB() {
		return $this->connectionProvider->getConnection();
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
			$success = $this->getDB()->query( $query );

			if ( $success === false ) {
				throw new FieldRemovalFailedException(
					$tableName,
					$fieldName,
					$this->getDB()->lastQuery()
				);
			}
		}

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
		$success = $this->getDB()->query(
			$this->sqlBuilder->getAddFieldSql( $tableName, $field ),
			__METHOD__
		);

		if ( $success === false ) {
			throw new FieldAdditionFailedException(
				$tableName,
				$field,
				$this->getDB()->lastQuery()
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
		$success = $this->getDB()->query(
			$this->sqlBuilder->getRemoveIndexSql( $tableName, $indexName ),
			__METHOD__
		);

		if ( $success === false ) {
			throw new IndexRemovalFailedException(
				$tableName,
				$indexName,
				$this->getDB()->lastQuery()
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
		$success = $this->getDB()->query(
			$this->sqlBuilder->getAddIndexSql( $tableName, $index ),
			__METHOD__
		);

		if ( $success === false ) {
			throw new IndexAdditionFailedException(
				$tableName,
				$index,
				$this->getDB()->lastQuery()
			);
		}
	}

}
