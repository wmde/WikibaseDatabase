<?php

namespace Wikibase\Database\Tests\PDO;

use PDO;
use Wikibase\Database\MySQL\MySQLTableDefinitionReader;
use Wikibase\Database\NullTableNameFormatter;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;

/**
 * @group Wikibase
 * @group WikibaseDatabase
 * @group Integration
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PDOSchemaModifierTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var PDO
	 */
	private $pdo;

	/**
	 * @var TableDefinition
	 */
	private $table;

	public function setUp() {
		$this->pdo = PDOIntegrationFactory::newPDO( $this );

		$this->table = new TableDefinition(
			'users',
			array(
				new FieldDefinition(
					'name',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						23
					)
				),
				new FieldDefinition(
					'email',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						42
					)
				),
			),
			array(
				new IndexDefinition(
					'name_index',
					array( 'name' ),
					IndexDefinition::TYPE_UNIQUE
				)
			)
		);

		$tableBuilder = $this->getTableBuilder();

		if ( $tableBuilder->tableExists( $this->table->getName() ) ) {
			$tableBuilder->dropTable( $this->table->getName() );
		}

		$tableBuilder->createTable( $this->table );
	}

	private function getTableBuilder() {
		$factory = new PDOFactory( $this->pdo );
		return $factory->newMySQLTableBuilder( PDOIntegrationFactory::DB_NAME );
	}

	private function newSchemaModifier() {
		$factory = new PDOFactory( $this->pdo );
		return $factory->newMySQLSchemaModifier();
	}

	private function readTable() {
		return $this->newSchemaReader()->readDefinition( $this->table->getName() );
	}

	private function newSchemaReader() {
		$factory = new PDOFactory( $this->pdo );
		return new MySQLTableDefinitionReader(
			$factory->newMySQLQueryInterface(),
			new NullTableNameFormatter()
		);
	}

	public function testAdditionOfNewFieldWorks() {
		$this->newSchemaModifier()->addField(
			$this->table->getName(),
			new FieldDefinition(
				'post_count',
				TypeDefinition::TYPE_INTEGER,
				FieldDefinition::NOT_NULL,
				0
			)
		);

		// TODO
		$this->assertTrue( true );
		//$this->assertTrue( $this->readTable()->hasFieldWithName( 'post_count' ) );
	}

	public function testAdditionOfFieldWithExistingNameCausesException() {
		$this->setExpectedException( 'Wikibase\Database\Schema\FieldAdditionFailedException' );

		$this->newSchemaModifier()->addField(
			$this->table->getName(),
			current( $this->table->getFields() )
		);
	}

	public function testGivenExistingField_fieldRemovalRemovesIt() {
		$this->newSchemaModifier()->removeField(
			$this->table->getName(),
			'name'
		);

		// TODO
		$this->assertTrue( true );
	}

	public function testGivenNonExistingField_fieldRemovalCausesException() {
		$this->setExpectedException( 'Wikibase\Database\Schema\FieldRemovalFailedException' );

		$this->newSchemaModifier()->removeField(
			$this->table->getName(),
			'kittens'
		);
	}

	public function testGivenNewIndex_indexAdditionWorks() {
		$this->newSchemaModifier()->addIndex(
			$this->table->getName(),
			new IndexDefinition(
				'name_email_index',
				array( 'name', 'email' )
			)
		);

		// TODO
		$this->assertTrue( true );
	}

	public function testAdditionOfIndexWithExistingNameCausesException() {
		$this->setExpectedException( 'Wikibase\Database\Schema\IndexAdditionFailedException' );

		$this->newSchemaModifier()->addIndex(
			$this->table->getName(),
			current( $this->table->getIndexes() )
		);
	}

	public function testGivenExistingIndex_indexRemovalWorks() {
		$this->newSchemaModifier()->removeIndex(
			$this->table->getName(),
			'name_index'
		);

		// TODO
		$this->assertTrue( true );
	}

	public function testGivenNonExistingIndex_indexRemovalCausesException() {
		$this->setExpectedException( 'Wikibase\Database\Schema\IndexRemovalFailedException' );

		$this->newSchemaModifier()->removeIndex(
			$this->table->getName(),
			'kittens'
		);
	}

}
