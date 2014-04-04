<?php

namespace Wikibase\Database\Tests\Schema\Definition;

use ReflectionClass;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;

/**
 * @covers Wikibase\Database\Schema\Definitions\FieldDefinition
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
class FieldDefinitionTest extends \PHPUnit_Framework_TestCase {

	public function instanceProvider() {
		$constructorArgs = array();

		$constructorArgs[] = array(
			'names',
			new TypeDefinition( TypeDefinition::TYPE_BLOB )
		);

		$constructorArgs[] = array(
			'stuffs',
			new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
			FieldDefinition::NOT_NULL,
			42
		);

		$constructorArgs[] = array(
			'stuffs',
			new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
			FieldDefinition::NULL,
			FieldDefinition::NO_DEFAULT
		);

		$constructorArgs[] = array(
			'stuffs',
			new TypeDefinition( TypeDefinition::TYPE_BIGINT ),
			FieldDefinition::NULL,
			FieldDefinition::NO_DEFAULT,
			FieldDefinition::AUTOINCREMENT
		);

		$argLists = array();

		foreach ( $constructorArgs as $constructorArgList ) {
			$argLists[] = array( $constructorArgList );
		}

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testConstructorSetsValues( array $constructorArgs ) {
		$class = new ReflectionClass( 'Wikibase\Database\Schema\Definitions\FieldDefinition' );
		$field = $class->newInstanceArgs( $constructorArgs );

		$this->assertEquals(
			$constructorArgs[0],
			$field->getName(),
			'The FieldDefinition name is set and obtained correctly'
		);

		$this->assertEquals(
			$constructorArgs[1],
			$field->getType(),
			'The FieldDefinition type is set and obtained correctly'
		);

		$this->assertEquals(
			array_key_exists( 2, $constructorArgs ) ? $constructorArgs[2] : FieldDefinition::NULL,
			$field->allowsNull(),
			'The FieldDefinition allowsNull is set and obtained correctly'
		);

		$this->assertEquals(
			array_key_exists( 3, $constructorArgs ) ? $constructorArgs[3] : FieldDefinition::NO_DEFAULT,
			$field->getDefault(),
			'The FieldDefinition default is set and obtained correctly'
		);

		$this->assertEquals(
			array_key_exists( 4, $constructorArgs ) ? $constructorArgs[4] : FieldDefinition::NO_AUTOINCREMENT,
			$field->hasAutoIncrement(),
			'The FieldDefinition autoIncrement is set and obtained correctly'
		);
	}

	public static function invalidNameProvider() {
		return array(
			array( 12 ),
			array( array() ),
			array( null ),
			array( true ),
			array( new \Exception() ),
		);
	}

	/**
	 * @dataProvider invalidNameProvider
	 */
	public function testInvalidName( $name ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new FieldDefinition( $name, new TypeDefinition( TypeDefinition::TYPE_INTEGER ) );
	}

	public static function invalidTypeProvider() {
		return array(
			array( 12 ),
			array( array() ),
			array( null ),
			array( true ),
			array( new \Exception() ),
		);
	}

	/**
	 * @dataProvider invalidTypeProvider
	 */
	public function testInvalidType( $type ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new FieldDefinition( 'name', $type );
	}

	public static function invalidBoolProvider() {
		return array(
			array( 12 ),
			array( array() ),
			array( null ),
			array( new \Exception() ),
		);
	}

	/**
	 * @dataProvider invalidBoolProvider
	 */
	public function testInvalidNull( $null ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new FieldDefinition( 'name', new TypeDefinition( TypeDefinition::TYPE_INTEGER ), $null );
	}

	/**
	 * @dataProvider invalidBoolProvider
	 */
	public function testInvalidAutoIncrement( $autoinc ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new FieldDefinition(
			'name',
			new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
			FieldDefinition::NULL,
			FieldDefinition::NO_DEFAULT,
			$autoinc
		);
	}

	public function testWhenConstructorJustGivenTypeName_getTypeReturnsTypeObject() {
		$field = new FieldDefinition( 'fieldName', TypeDefinition::TYPE_INTEGER );

		$this->assertEquals(
			new TypeDefinition( TypeDefinition::TYPE_INTEGER ),
			$field->getType()
		);
	}

}
