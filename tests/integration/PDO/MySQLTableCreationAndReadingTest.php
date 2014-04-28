<?php

namespace Wikibase\Database\Tests\PDO;

use PDO;
use Wikibase\Database\MySQL\MySQLTableDefinitionReader;
use Wikibase\Database\NullTableNameFormatter;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
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
class MySQLTableCreationAndReadingTest extends \PHPUnit_Framework_TestCase {

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
			'people',
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

	public function testTableExists() {
		$this->assertTrue( $this->getTableBuilder()->tableExists( $this->table->getName() ) );
	}

	public function testCanReadTable() {
		// TODO
		// $table = $this->readTable();
		$this->assertTrue( true );
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

}
