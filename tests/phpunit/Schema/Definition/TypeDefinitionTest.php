<?php

namespace Wikibase\Database\Tests\Schema\Definition;

use ReflectionClass;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;

/**
 * @covers Wikibase\Database\Schema\Definitions\TypeDefinition
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class TypeDefinitionTest extends \PHPUnit_Framework_TestCase {

	public function instanceProvider() {
		$constructorArgs = array();

		$constructorArgs[] = array(
			TypeDefinition::TYPE_BLOB
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_DECIMAL
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_FLOAT
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_TINYINT
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_BIGINT
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_INTEGER
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_VARCHAR
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_INTEGER,
			TypeDefinition::NO_SIZE,
			TypeDefinition::NO_ATTRIB
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_INTEGER,
			TypeDefinition::NO_SIZE,
			TypeDefinition::ATTRIB_BINARY
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_INTEGER,
			TypeDefinition::NO_SIZE,
			TypeDefinition::ATTRIB_UNSIGNED
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_BLOB,
			255
		);

		$constructorArgs[] = array(
			TypeDefinition::TYPE_VARCHAR,
			10
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
		$class = new ReflectionClass( 'Wikibase\Database\Schema\Definitions\TypeDefinition' );
		/** @var TypeDefinition $type */
		$type = $class->newInstanceArgs( $constructorArgs );

		$this->assertEquals(
			$constructorArgs[0],
			$type->getName(),
			'The TypeDefinition name is set and obtained correctly'
		);

		$this->assertEquals(
			array_key_exists( 1, $constructorArgs ) ? $constructorArgs[1] : TypeDefinition::NO_SIZE,
			$type->getSize(),
			'The TypeDefinition size is set and obtained correctly'
		);

		$this->assertEquals(
			array_key_exists( 2, $constructorArgs ) ? $constructorArgs[2] : TypeDefinition::NO_ATTRIB,
			$type->getAttributes(),
			'The TypeDefinition attributes is set and obtained correctly'
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
		new TypeDefinition( $name );
	}

	public static function invalidSizeProvider(){
		return array(
			array( array() ),
			array( null ),
			array( true ),
			array( new \Exception() ),
			array( 'adsfdg' ),
		);
	}

	/**
	 * @dataProvider invalidSizeProvider
	 */
	public function testInvalidType( $size ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new TypeDefinition( TypeDefinition::TYPE_BLOB, $size );
	}

}
