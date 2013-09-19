<?php

namespace Wikibase\Database\MediaWiki;

use DatabaseBase;
use Wikibase\Database\DBConnectionProvider;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
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
	 * TODO: document throws
	 */
	public function removeField( $tableName, $fieldName ) {
		// TODO
		// $this->getDB()->query( $this->sqlBuilder->getRemoveFieldSql( $tableName, $fieldName ) );
	}

	/**
	 * @see SchemaModifier::addField
	 *
	 * @param string $tableName
	 * @param FieldDefinition $field
	 *
	 * TODO: document throws
	 */
	public function addField( $tableName, FieldDefinition $field ) {
		// TODO
	}

}


