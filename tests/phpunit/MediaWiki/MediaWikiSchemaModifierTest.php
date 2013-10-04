<?php

namespace Wikibase\Database\Tests\MediaWiki;

use Wikibase\Database\MediaWiki\MediaWikiSchemaModifier;

/**
 * @covers Wikibase\Database\MediaWiki\MediaWikiSchemaModifier
 *
 * @group Wikibase
 * @group WikibaseDatabase
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiSchemaModifierTest extends \PHPUnit_Framework_TestCase {

	protected function getMockConnectionProvider( $connection ) {
		$connectionProvider = $this->getMock( 'Wikibase\Database\DBConnectionProvider' );

		$connectionProvider->expects( $this->any() )
			->method( 'getConnection' )
			->will( $this->returnValue( $connection ) );

		return $connectionProvider;
	}

	public function testRemoveField() {
		$tableName = 'tableName';
		$fieldName = 'fieldName';
		$sql = 'foo bar baz';

		$sqlBuilder = $this->getMock( 'Wikibase\Database\Schema\SchemaModificationSqlBuilder' );
		$sqlBuilder->expects( $this->once() )
			->method( 'getRemoveFieldSql' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( $fieldName )
			)
			->will( $this->returnValue( $sql ) );

		$connection = $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'query' )
			->with( $this->equalTo( $sql ) )
			->will( $this->returnValue( true ) );

		$connectionProvider = $this->getMockConnectionProvider( $connection );

		$modifier = new MediaWikiSchemaModifier( $connectionProvider, $sqlBuilder );

		$modifier->removeField( $tableName, $fieldName );
	}

	public function testAddField() {
		$tableName = 'fieldName';
		$field = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\FieldDefinition' )
			->disableOriginalConstructor()->getMock();
		$sql = 'foo bar baz';

		$sqlBuilder = $this->getMock( 'Wikibase\Database\Schema\SchemaModificationSqlBuilder' );
		$sqlBuilder->expects( $this->once() )
			->method( 'getAddFieldSql' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( $field )
			)
			->will( $this->returnValue( $sql ) );

		$connection = $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'query' )
			->with( $this->equalTo( $sql ) )
			->will( $this->returnValue( true ) );

		$connectionProvider = $this->getMockConnectionProvider( $connection );

		$modifier = new MediaWikiSchemaModifier( $connectionProvider, $sqlBuilder );

		$modifier->addField( $tableName, $field );
	}

	public function testRemoveFieldThrowsExceptionOnQueryFailure() {
		$sqlBuilder = $this->getMock( 'Wikibase\Database\Schema\SchemaModificationSqlBuilder' );

		$connection = $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'query' )
			->will( $this->returnValue( false ) );

		$connectionProvider = $this->getMockConnectionProvider( $connection );

		$modifier = new MediaWikiSchemaModifier( $connectionProvider, $sqlBuilder );

		$this->setExpectedException( 'Wikibase\Database\Schema\FieldRemovalFailedException' );
		$modifier->removeField( 'foo', 'bar' );
	}

	public function testAddFieldThrowsExceptionOnQueryFailure() {
		$sqlBuilder = $this->getMock( 'Wikibase\Database\Schema\SchemaModificationSqlBuilder' );

		$connection = $this->getMock( 'DatabaseMysql' );

		$connection->expects( $this->once() )
			->method( 'query' )
			->will( $this->returnValue( false ) );

		$connectionProvider = $this->getMockConnectionProvider( $connection );

		$modifier = new MediaWikiSchemaModifier( $connectionProvider, $sqlBuilder );

		$field = $this->getMockBuilder( 'Wikibase\Database\Schema\Definitions\FieldDefinition' )
			->disableOriginalConstructor()->getMock();

		$this->setExpectedException( 'Wikibase\Database\Schema\FieldAdditionFailedException' );
		$modifier->addField( 'foo', $field );
	}

}
