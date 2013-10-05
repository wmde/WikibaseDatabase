<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\FieldAdditionFailedException;
use Wikibase\Database\Schema\FieldRemovalFailedException;
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
		$success = $this->getDB()->query(
			$this->sqlBuilder->getRemoveFieldSql( $tableName, $fieldName ),
			__METHOD__
		);

		if ( $success === false ) {
			throw new FieldRemovalFailedException(
				$tableName,
				$fieldName,
				$this->getDB()->lastQuery()
			);
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
	 * TODO: document throws
	 */
	public function removeIndex( $tableName, $indexName ) {
		// TODO
	}

	/**
	 * @see SchemaModifier::addIndex
	 *
	 * @param string $tableName
	 * @param IndexDefinition $index
	 *
	 * TODO: document throws
	 */
	public function addIndex( $tableName, IndexDefinition $index ) {
		// TODO
	}

}
