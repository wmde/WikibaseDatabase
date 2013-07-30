<?php

namespace Wikibase\Database\Tests;

use Wikibase\Database\IndexDefinition;

/**
 * @covers Wikibase\Database\IndexDefinition
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseDatabaseTest
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Denny Vrandecic < vrandecic@gmail.com >
 */
class IndexDefinitionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider validNameProvider
	 */
	public function testCanGetName( $indexName ) {
		$index = new IndexDefinition( $indexName, array( 'a' => 0 ) );

		$this->assertEquals( $indexName, $index->getName() );
	}

	public function validNameProvider() {
		return array(
			array( 'f' ),
			array( 'f' ),
			array( 'foo_bar' ),
			array( 'foo_bar_baz' ),
			array( 'foobarbazbah' ),
		);
	}

	/**
	 * @dataProvider invalidNameProvider
	 */
	public function testCannotSetInvalidName( $invalidName ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new IndexDefinition( $invalidName, array( 'a' => 0 ) );
	}

	public function invalidNameProvider() {
		return array(
			array( null ),
			array( array() ),
			array( false ),
			array( 42 ),
			array( 4.2 ),
			array( (object)array( 'foo' => 'bar' ) ),
			array( '' ),
			array( 'foo bar' ),
			array( ' ' ),
			array( '.' ),
			array( '42?' ),
			array( '$' ),
			array( '"foo"' ),
		);
	}

	public function testCanGetColumns() {
		$indexName = 'foo_bar';
		$columns = array(
			'foo' => 10
		);

		$index = new IndexDefinition( $indexName, $columns );

		$this->assertEquals( $columns, $index->getColumns() );
	}

	/**
	 * @dataProvider invalidColumnsProvider
	 */
	public function testCannotSetInvalidColumns( $invalidColumns ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new IndexDefinition( 'name', $invalidColumns );
	}

	public function invalidColumnsProvider() {
		return array(
			array( null ),
			array( array() ),
			array( false ),
			array( 42 ),
			array( 4.2 ),
			array( (object)array( 'foo' => 'bar' ) ),
			array( '' ),
			array( '"foo"' ),
			array( array( 'name' ) ),
			array( array( 0 => 'name' ) ),
			array( array( 'name' => 'name' ) ),
			array( array( 'name' => 4.2 ) ),
			array( array( 'name' => -1 ) ),
			array( array( 'name' => -1337 ) ),
		);
	}

	public function testDefaultIndexTypeIsSet() {
		$index = new IndexDefinition( 'foo_bar', array( 'a' => 0 ) );

		$this->assertEquals( IndexDefinition::TYPE_INDEX, $index->getType() );
	}

	public function testGetSetIndexType() {
		$indexType = IndexDefinition::TYPE_FULLTEXT;

		$index = new IndexDefinition( 'foo_bar', array( 'a' => 0 ), $indexType );

		$this->assertEquals( $indexType, $index->getType() );
	}

	/**
	 * @dataProvider invalidIndexTypeProvider
	 */
	public function testCannotSetInvalidIndexType( $notAnIndexType ) {
		$this->setExpectedException( 'InvalidArgumentException' );
		new IndexDefinition( 'foo_bar', array( 'a' => 0 ), $notAnIndexType );
	}

	public function invalidIndexTypeProvider() {
		return array(
			array( null ),
			array( array() ),
			array( false ),
			array( 42 ),
			array( 4.2 ),
			array( (object)array( 'foo' => 'bar' ) ),
			array( '' ),
			array( 'foo' ),
		);
	}

}
