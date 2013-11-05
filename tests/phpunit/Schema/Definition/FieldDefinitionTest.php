<?php

namespace Wikibase\Database\Tests\Schema\Definition;

use ReflectionClass;
use Wikibase\Database\Schema\Definitions\FieldDefinition;

/**
 * @covers Wikibase\Database\Schema\Definitions\FieldDefinition
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FieldDefinitionTest extends \PHPUnit_Framework_TestCase {

	public function instanceProvider() {
		$constructorArgs = array();

		$constructorArgs[] = array(
			'names',
			FieldDefinition::TYPE_TEXT
		);

		$constructorArgs[] = array(
			'numbers',
			FieldDefinition::TYPE_FLOAT
		);

		$constructorArgs[] = array(
			'stuffs',
			FieldDefinition::TYPE_INTEGER,
			FieldDefinition::NOT_NULL,
			42,
			FieldDefinition::ATTRIB_UNSIGNED
		);

		$constructorArgs[] = array(
			'stuffs',
			FieldDefinition::TYPE_INTEGER,
			FieldDefinition::NULL,
			FieldDefinition::NO_DEFAULT,
			FieldDefinition::NO_ATTRIB
		);

		$constructorArgs[] = array(
			'stuffs',
			FieldDefinition::TYPE_INTEGER,
			FieldDefinition::NULL,
			FieldDefinition::NO_DEFAULT,
			FieldDefinition::NO_ATTRIB,
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
			array_key_exists( 4, $constructorArgs ) ? $constructorArgs[4] : FieldDefinition::NO_ATTRIB,
			$field->getAttributes(),
			'The FieldDefinition attributes are set and obtained correctly'
		);

		$this->assertEquals(
			array_key_exists( 5, $constructorArgs ) ? $constructorArgs[5] : FieldDefinition::NO_AUTOINCREMENT,
			$field->hasAutoIncrement(),
			'The FieldDefinition autoIncrement is set and obtained correctly'
		);
	}

	public static function invalidNameProvider(){
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
		new FieldDefinition( $name, FieldDefinition::TYPE_INTEGER );
	}

	public static function invalidTypeProvider(){
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

	public static function invalidBoolProvider(){
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
		new FieldDefinition( 'name', FieldDefinition::TYPE_INTEGER, $null );
	}

	/**
	 * @dataProvider invalidBoolProvider
	 */
	public function testInvalidAutoIncrement( $autoinc ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new FieldDefinition(
			'name',
			FieldDefinition::TYPE_INTEGER,
			FieldDefinition::NULL,
			FieldDefinition::NO_DEFAULT,
			FieldDefinition::NO_ATTRIB,
			$autoinc
		);
	}
}
