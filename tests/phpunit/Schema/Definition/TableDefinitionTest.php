<?php

namespace Wikibase\Database\Tests\Schema\Definition;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * @covers Wikibase\Database\Schema\Definitions\TableDefinition
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TableDefinitionTest extends \PHPUnit_Framework_TestCase {

	public function instanceProvider() {
		$instances = array();

		$instances[] = new TableDefinition(
			'snaks',
			array(
				new FieldDefinition( 'omnomnom', FieldDefinition::TYPE_TEXT )
			)
		);

		$instances[] = new TableDefinition(
			'spam',
			array(
				new FieldDefinition( 'o', FieldDefinition::TYPE_TEXT ),
				new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ),
				new FieldDefinition( 'i', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42 ),
				new FieldDefinition( 'bi', FieldDefinition::TYPE_BIGINT, FieldDefinition::NOT_NULL, 42 ),
			)
		);

		$instances[] = new TableDefinition(
			'spam',
			array(
				new FieldDefinition( 'o', FieldDefinition::TYPE_TEXT ),
				new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ),
				new FieldDefinition( 'i', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42 ),
			),
			array(
				new IndexDefinition( 'o', array( 'o' => 0 ) ),
			)
		);

		$instances[] = new TableDefinition(
			'spam',
			array(
				new FieldDefinition( 'o', FieldDefinition::TYPE_TEXT ),
			),
			array(
				new IndexDefinition( 'o', array( 'o' => 0 ) ),
				new IndexDefinition( 'h', array( 'h' => 42 ) ),
				new IndexDefinition( 'foo', array( 'bar' => 5, 'baz' => 0 ) ),
			)
		);

		$argLists = array();

		foreach ( $instances as $instance ) {
			$argLists[] = array( $instance );
		}

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testReturnValueOfGetName( TableDefinition $table ) {
		$this->assertInternalType( 'string', $table->getName() );

		$newTable = new TableDefinition( $table->getName(), $table->getFields() );

		$this->assertEquals(
			$table->getName(),
			$newTable->getName(),
			'The TableDefinition name is set and obtained correctly'
		);
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testReturnValueOfGetFields( TableDefinition $table ) {
		$this->assertInternalType( 'array', $table->getFields() );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\Database\Schema\Definitions\FieldDefinition', $table->getFields() );

		foreach ( $table->getFields() as $expectedName => $field ) {
			$this->assertEquals(
				$expectedName,
				$field->getName(),
				'The array key matches the corresponding field name'
			);
		}

		$newTable = new TableDefinition( $table->getName(), $table->getFields() );

		$this->assertEquals(
			$table->getFields(),
			$newTable->getFields(),
			'The TableDefinition fields are set and obtained correctly'
		);
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testReturnValueOfHasField( TableDefinition $table ) {
		foreach ( $table->getFields() as $field ) {
			$this->assertTrue( $table->hasFieldWithName( $field->getName() ) );
		}

		$this->assertFalse( $table->hasFieldWithName( 'zsfrcvbxuyiyrewrbmndsrbtfocszdf' ) );
		$this->assertFalse( $table->hasFieldWithName( '' ) );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testMutateName( TableDefinition $table ) {
		$newTable = $table->mutateName( $table->getName() );

		$this->assertInstanceOf( get_class( $table ), $newTable );
		$this->assertEquals( $table, $newTable );

		$newTable = $table->mutateName( 'foobarbaz' );

		$this->assertEquals( 'foobarbaz', $newTable->getName() );
		$this->assertEquals( $table->getFields(), $newTable->getFields() );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testMutateFields( TableDefinition $table ) {
		$newTable = $table->mutateFields( $table->getFields() );

		$this->assertInstanceOf( get_class( $table ), $newTable );
		$this->assertEquals( $table, $newTable );

		$fields = array(
			new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ),
			new FieldDefinition( 'a', FieldDefinition::TYPE_TINYINT ),
			new FieldDefinition( 'x', FieldDefinition::TYPE_INTEGER ),
		);

		$newTable = $table->mutateFields( $fields );

		$this->assertEquals( $fields, array_values( $newTable->getFields() ) );
		$this->assertEquals( $table->getName(), $newTable->getName() );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param TableDefinition $table
	 */
	public function testReturnTypeOfGetIndexes( TableDefinition $table ) {
		$this->assertInternalType( 'array', $table->getIndexes() );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\Database\Schema\Definitions\IndexDefinition', $table->getIndexes() );

		$newTable = new TableDefinition( $table->getName(), $table->getFields(), $table->getIndexes() );

		$this->assertEquals(
			$table->getIndexes(),
			$newTable->getIndexes(),
			'The TableDefinition indexes are set and obtained correctly'
		);
	}

	/**
	 * @dataProvider invalidIndexesProvider
	 */
	public function testCannotConstructWithInvalidIndexList( array $invalidIndexList ) {
		$this->setExpectedException( 'InvalidArgumentException' );

		new TableDefinition(
			'foo',
			array( new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ) ),
			$invalidIndexList
		);
	}

	public function invalidIndexesProvider() {
		$mockIndexDefinition = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\IndexDefinition' )
			->disableOriginalConstructor()->getMock();

		$argLists = array();

		$argLists[] = array( array(
			null
		) );

		$argLists[] = array( array(
			'foo bar'
		) );

		$argLists[] = array( array(
			$mockIndexDefinition,
			array()
		) );

		$argLists[] = array( array(
			$mockIndexDefinition,
			$mockIndexDefinition,
			4.2,
			$mockIndexDefinition,
			$mockIndexDefinition
		) );

		return $argLists;
	}

	/**
	 * @dataProvider mutateFieldAwayProvider
	 */
	public function testMutateFieldAway( $toRemove, TableDefinition $definition, TableDefinition $expected ){
		$newDefinition = $definition->mutateFieldAway( $toRemove );
		$this->assertEquals( $expected, $newDefinition );
	}

	public function mutateFieldAwayProvider() {
		$args = array(
			array( 'o',
				new TableDefinition(
					'spam',
					array(
						new FieldDefinition( 'o', FieldDefinition::TYPE_TEXT ),
						new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ),
						new FieldDefinition( 'i', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42 ),
					)
				),
				new TableDefinition(
					'spam',
					array(
						new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ),
						new FieldDefinition( 'i', FieldDefinition::TYPE_INTEGER, FieldDefinition::NOT_NULL, 42 ),
					)
				),
			),
			array( 'o',
				new TableDefinition(
					'spam',
					array(
						new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ),
					)
				),
				new TableDefinition(
					'spam',
					array(
						new FieldDefinition( 'h', FieldDefinition::TYPE_TEXT ),
					)
				),
			),
		);

		return $args;
	}

	/**
	 * @dataProvider mutateIndexAwayProvider
	 */
	public function testMutateIndexes( $toRemove, TableDefinition $definition, TableDefinition $expected ){
		$newIndexes = array();
		foreach( $definition->getIndexes() as $index ){
			if( $index->getName() !== $toRemove ){
				$newIndexes[] = $index;
			}
		}
		$newDefinition = $definition->mutateIndexes( $newIndexes );
		$this->assertEquals( $expected, $newDefinition );
	}

	/**
	 * @dataProvider mutateIndexAwayProvider
	 */
	public function testMutateIndexAway( $toRemove, TableDefinition $definition, TableDefinition $expected ){
		$newDefinition = $definition->mutateIndexAway( $toRemove );
		$this->assertEquals( $expected, $newDefinition );
	}

	public function mutateIndexAwayProvider() {
		$args = array(
			array( 'o',
				new TableDefinition(
					'spam',
					array( new FieldDefinition( 'foo', FieldDefinition::TYPE_TINYINT ) ),
					array(
						new IndexDefinition( 'o', array( 'a' => 1 ), IndexDefinition::TYPE_INDEX ),
						new IndexDefinition( 'h', array( 'b' => 2 ), IndexDefinition::TYPE_PRIMARY ),
					)
				),
				new TableDefinition(
					'spam',
					array( new FieldDefinition( 'foo', FieldDefinition::TYPE_TINYINT ) ),
					array( new IndexDefinition( 'h', array( 'b' => 2 ), IndexDefinition::TYPE_PRIMARY ) )
				),
			),
			array( 'o',
				new TableDefinition(
					'spam',
					array( new FieldDefinition( 'foo', FieldDefinition::TYPE_TINYINT ) ),
					array( new IndexDefinition( 'h', array( 'b' => 2 ), IndexDefinition::TYPE_PRIMARY ) )
				),
				new TableDefinition(
					'spam',
					array( new FieldDefinition( 'foo', FieldDefinition::TYPE_TINYINT ) ),
					array( new IndexDefinition( 'h', array( 'b' => 2 ), IndexDefinition::TYPE_PRIMARY ) )
				),
			),
		);
		return $args;
	}

}
